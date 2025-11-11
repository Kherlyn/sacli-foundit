<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->category = Category::factory()->create();
});

describe('Item Model Relationships', function () {
  it('belongs to a user', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    expect($item->user)->toBeInstanceOf(User::class);
    expect($item->user->id)->toBe($this->user->id);
  });

  it('belongs to a category', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    expect($item->category)->toBeInstanceOf(Category::class);
    expect($item->category->id)->toBe($this->category->id);
  });

  it('has many images', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    ItemImage::factory()->count(3)->create(['item_id' => $item->id]);

    expect($item->images)->toHaveCount(3);
    expect($item->images->first())->toBeInstanceOf(ItemImage::class);
  });

  it('user has many items', function () {
    Item::factory()->count(2)->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    expect($this->user->items)->toHaveCount(2);
    expect($this->user->items->first())->toBeInstanceOf(Item::class);
  });
});

describe('Item Model Scopes', function () {
  beforeEach(function () {
    // Create items with different statuses
    Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'status' => 'pending',
      'title' => 'Lost Phone',
      'description' => 'Black iPhone lost in park',
      'type' => 'lost',
      'location' => 'Central Park',
      'date_occurred' => now()->subDays(2),
    ]);

    Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'status' => 'verified',
      'title' => 'Found Wallet',
      'description' => 'Brown leather wallet found',
      'type' => 'found',
      'location' => 'Downtown Mall',
      'date_occurred' => now()->subDays(1),
    ]);

    Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'status' => 'rejected',
      'title' => 'Lost Keys',
      'description' => 'Car keys with blue keychain',
      'type' => 'lost',
      'location' => 'Office Building',
      'date_occurred' => now()->subDays(3),
    ]);
  });

  it('filters verified items', function () {
    $verifiedItems = Item::verified()->get();

    expect($verifiedItems)->toHaveCount(1);
    expect($verifiedItems->first()->status)->toBe('verified');
  });

  it('filters pending items', function () {
    $pendingItems = Item::pending()->get();

    expect($pendingItems)->toHaveCount(1);
    expect($pendingItems->first()->status)->toBe('pending');
  });

  it('filters public items (verified)', function () {
    $publicItems = Item::public()->get();

    expect($publicItems)->toHaveCount(1);
    expect($publicItems->first()->status)->toBe('verified');
  });

  it('searches items by title and description', function () {
    $phoneItems = Item::search('phone')->get();
    expect($phoneItems)->toHaveCount(1);
    expect($phoneItems->first()->title)->toBe('Lost Phone');

    $walletItems = Item::search('wallet')->get();
    expect($walletItems)->toHaveCount(1);
    expect($walletItems->first()->title)->toBe('Found Wallet');

    $leatherItems = Item::search('leather')->get();
    expect($leatherItems)->toHaveCount(1);
    expect($leatherItems->first()->description)->toContain('leather');
  });

  it('filters by item type', function () {
    $lostItems = Item::ofType('lost')->get();
    expect($lostItems)->toHaveCount(2);

    $foundItems = Item::ofType('found')->get();
    expect($foundItems)->toHaveCount(1);
  });

  it('filters by category', function () {
    $categoryItems = Item::inCategory($this->category->id)->get();
    expect($categoryItems)->toHaveCount(3);

    $nonExistentCategoryItems = Item::inCategory(999)->get();
    expect($nonExistentCategoryItems)->toHaveCount(0);
  });

  it('filters by date range', function () {
    $recentItems = Item::dateRange(now()->subDays(1))->get();
    expect($recentItems)->toHaveCount(1);

    $oldItems = Item::dateRange(null, now()->subDays(2))->get();
    expect($oldItems)->toHaveCount(2);

    $specificRangeItems = Item::dateRange(now()->subDays(2), now()->subDays(1))->get();
    expect($specificRangeItems)->toHaveCount(2);
  });

  it('filters by location', function () {
    $parkItems = Item::nearLocation('Park')->get();
    expect($parkItems)->toHaveCount(1);
    expect($parkItems->first()->location)->toContain('Park');

    $mallItems = Item::nearLocation('Mall')->get();
    expect($mallItems)->toHaveCount(1);
    expect($mallItems->first()->location)->toContain('Mall');
  });
});

describe('Item Model Validation', function () {
  it('has correct validation rules for creation', function () {
    $rules = Item::validationRules();

    expect($rules)->toHaveKey('title');
    expect($rules)->toHaveKey('description');
    expect($rules)->toHaveKey('category_id');
    expect($rules)->toHaveKey('type');
    expect($rules)->toHaveKey('location');
    expect($rules)->toHaveKey('date_occurred');
    expect($rules)->toHaveKey('contact_info');

    expect($rules['title'])->toContain('required');
    expect($rules['description'])->toContain('min:10');
    expect($rules['date_occurred'])->toContain('before_or_equal:today');
  });

  it('has correct validation rules for update', function () {
    $rules = Item::validationRules(true);

    expect($rules)->toHaveKey('status');
    expect($rules)->toHaveKey('admin_notes');
    expect($rules['status'])->toContain('sometimes');
    expect($rules['admin_notes'])->toContain('nullable');
  });

  it('has validation messages', function () {
    $messages = Item::validationMessages();

    expect($messages)->toHaveKey('title.required');
    expect($messages)->toHaveKey('description.min');
    expect($messages)->toHaveKey('date_occurred.before_or_equal');
    expect($messages['title.required'])->toBe('The item title is required.');
  });
});

describe('Item Model Helper Methods', function () {
  beforeEach(function () {
    $this->item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'status' => 'pending',
    ]);
  });

  it('checks if item is owned by user', function () {
    expect($this->item->isOwnedBy($this->user))->toBeTrue();

    $otherUser = User::factory()->create();
    expect($this->item->isOwnedBy($otherUser))->toBeFalse();
  });

  it('checks item status', function () {
    expect($this->item->isPending())->toBeTrue();
    expect($this->item->isVerified())->toBeFalse();
    expect($this->item->isResolved())->toBeFalse();
  });

  it('marks item as verified', function () {
    $this->item->markAsVerified('Looks legitimate');

    expect($this->item->fresh()->status)->toBe('verified');
    expect($this->item->fresh()->verified_at)->not->toBeNull();
    expect($this->item->fresh()->admin_notes)->toBe('Looks legitimate');
  });

  it('marks item as rejected', function () {
    $this->item->markAsRejected('Insufficient information');

    expect($this->item->fresh()->status)->toBe('rejected');
    expect($this->item->fresh()->admin_notes)->toBe('Insufficient information');
  });

  it('marks item as resolved', function () {
    $this->item->markAsResolved();

    expect($this->item->fresh()->status)->toBe('resolved');
    expect($this->item->fresh()->resolved_at)->not->toBeNull();
  });
});

describe('Item Duration Calculation', function () {
  it('calculates duration for items reported today', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'date_occurred' => now(),
    ]);

    expect($item->duration)->toContain('ago');
  });

  it('calculates duration in days for items between 1 and 30 days old', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'date_occurred' => now()->subDays(10),
    ]);

    expect($item->duration)->toContain('day');
    expect($item->duration)->toContain('ago');
  });

  it('calculates duration in months for items older than 30 days', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'date_occurred' => now()->subMonths(2),
    ]);

    expect($item->duration)->toContain('month');
    expect($item->duration)->toContain('ago');
  });

  it('returns appropriate CSS class for recent items', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'date_occurred' => now()->subDays(3),
    ]);

    expect($item->duration_class)->toContain('sacli-green');
    expect($item->duration_class)->toContain('font-semibold');
  });

  it('returns appropriate CSS class for moderate age items', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'date_occurred' => now()->subDays(15),
    ]);

    expect($item->duration_class)->toContain('gray-600');
  });

  it('returns appropriate CSS class for old items', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'date_occurred' => now()->subDays(45),
    ]);

    expect($item->duration_class)->toContain('gray-400');
  });
});
