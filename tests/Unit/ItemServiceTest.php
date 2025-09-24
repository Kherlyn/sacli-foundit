<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use App\Repositories\ItemRepository;
use App\Services\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->category = Category::factory()->create();
  $this->repository = new ItemRepository();
  $this->service = new ItemService($this->repository);

  // Fake the storage for file uploads
  Storage::fake('public');
});

describe('ItemService Item Creation', function () {
  it('creates an item with valid data', function () {
    $data = [
      'title' => 'Lost iPhone 12',
      'description' => 'Black iPhone 12 Pro lost in Central Park yesterday',
      'category_id' => $this->category->id,
      'type' => 'lost',
      'location' => 'Central Park, NYC',
      'date_occurred' => now()->subDay()->format('Y-m-d'),
      'contact_info' => [
        'method' => 'email',
        'email' => 'test@example.com',
      ],
      'user_id' => $this->user->id,
    ];

    $item = $this->service->createItem($data);

    expect($item)->toBeInstanceOf(Item::class);
    expect($item->title)->toBe('Lost iPhone 12');
    expect($item->status)->toBe('pending');
    expect($item->user_id)->toBe($this->user->id);
    expect($item->category_id)->toBe($this->category->id);
  });

  it('creates an item with images', function () {
    $data = [
      'title' => 'Found Wallet',
      'description' => 'Brown leather wallet found near subway station',
      'category_id' => $this->category->id,
      'type' => 'found',
      'location' => 'Subway Station',
      'date_occurred' => now()->subDay()->format('Y-m-d'),
      'contact_info' => [
        'method' => 'phone',
        'phone' => '+1234567890',
      ],
      'user_id' => $this->user->id,
    ];

    $images = [
      UploadedFile::fake()->image('wallet1.jpg', 800, 600),
      UploadedFile::fake()->image('wallet2.png', 600, 400),
    ];

    $item = $this->service->createItem($data, $images);

    expect($item)->toBeInstanceOf(Item::class);
    expect($item->images)->toHaveCount(2);
    expect($item->images->first())->toBeInstanceOf(ItemImage::class);

    // Check that files were stored
    Storage::disk('public')->assertExists('items/' . $item->id . '/' . $item->images->first()->filename);
  });

  it('validates item data and throws exception for invalid data', function () {
    $invalidData = [
      'title' => '', // Required field empty
      'description' => 'Short', // Too short
      'category_id' => 999, // Non-existent category
      'type' => 'invalid', // Invalid type
      'location' => '',
      'date_occurred' => now()->addDay()->format('Y-m-d'), // Future date
      'contact_info' => [], // Missing required contact info
      'user_id' => $this->user->id,
    ];

    expect(fn() => $this->service->createItem($invalidData))
      ->toThrow(ValidationException::class);
  });

  it('validates images and throws exception for invalid images', function () {
    $data = [
      'title' => 'Test Item',
      'description' => 'This is a test item with valid description',
      'category_id' => $this->category->id,
      'type' => 'lost',
      'location' => 'Test Location',
      'date_occurred' => now()->subDay()->format('Y-m-d'),
      'contact_info' => [
        'method' => 'email',
        'email' => 'test@example.com',
      ],
      'user_id' => $this->user->id,
    ];

    $invalidImages = [
      UploadedFile::fake()->create('document.pdf', 1000), // Not an image
      UploadedFile::fake()->image('large.jpg', 2000, 2000)->size(3000), // Too large
    ];

    expect(fn() => $this->service->createItem($data, $invalidImages))
      ->toThrow(ValidationException::class);
  });
});

describe('ItemService Item Updates', function () {
  beforeEach(function () {
    $this->item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);
  });

  it('updates an item with valid data', function () {
    $updateData = [
      'title' => 'Updated Title',
      'description' => 'Updated description with more details about the item',
      'location' => 'Updated Location',
    ];

    $updatedItem = $this->service->updateItem($this->item, $updateData);

    expect($updatedItem->title)->toBe('Updated Title');
    expect($updatedItem->description)->toBe('Updated description with more details about the item');
    expect($updatedItem->location)->toBe('Updated Location');
    expect($updatedItem->category_id)->toBe($this->item->category_id); // Unchanged
  });

  it('updates an item with new images', function () {
    $updateData = [
      'title' => 'Updated with Images',
    ];

    $images = [
      UploadedFile::fake()->image('new_image.jpg', 600, 400),
    ];

    $updatedItem = $this->service->updateItem($this->item, $updateData, $images);

    expect($updatedItem->title)->toBe('Updated with Images');
    expect($updatedItem->images)->toHaveCount(1);
  });
});

describe('ItemService Admin Actions', function () {
  beforeEach(function () {
    $this->item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);
  });

  it('verifies an item', function () {
    $verifiedItem = $this->service->verifyItem($this->item, 'Item looks legitimate');

    expect($verifiedItem->status)->toBe('verified');
    expect($verifiedItem->admin_notes)->toBe('Item looks legitimate');
    expect($verifiedItem->verified_at)->not->toBeNull();
  });

  it('rejects an item', function () {
    $rejectedItem = $this->service->rejectItem($this->item, 'Insufficient information');

    expect($rejectedItem->status)->toBe('rejected');
    expect($rejectedItem->admin_notes)->toBe('Insufficient information');
  });

  it('resolves an item', function () {
    $this->item->markAsVerified();

    $resolvedItem = $this->service->resolveItem($this->item);

    expect($resolvedItem->status)->toBe('resolved');
    expect($resolvedItem->resolved_at)->not->toBeNull();
  });

  it('bulk updates item statuses', function () {
    $items = Item::factory()->count(3)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $itemIds = $items->pluck('id')->toArray();

    $updatedCount = $this->service->bulkUpdateStatus($itemIds, 'verified', 'Bulk verification');

    expect($updatedCount)->toBe(3);

    foreach ($items as $item) {
      $item->refresh();
      expect($item->status)->toBe('verified');
      expect($item->admin_notes)->toBe('Bulk verification');
    }
  });

  it('throws exception for invalid bulk status', function () {
    $itemIds = [1, 2, 3];

    expect(fn() => $this->service->bulkUpdateStatus($itemIds, 'invalid_status'))
      ->toThrow(\InvalidArgumentException::class);
  });
});

describe('ItemService Search and Filtering', function () {
  beforeEach(function () {
    Item::factory()->count(2)->verified()->lost()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'title' => 'Lost Phone',
    ]);

    Item::factory()->verified()->found()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'title' => 'Found Wallet',
    ]);
  });

  it('searches items with filters', function () {
    $filters = [
      'query' => 'phone',
      'type' => 'lost',
    ];

    $result = $this->service->searchItems($filters);

    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->type)->toBe('lost');
      expect($item->title)->toContain('Phone');
    }
  });

  it('gets admin items with filters', function () {
    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $filters = ['status' => 'pending'];
    $result = $this->service->getAdminItems($filters);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->status)->toBe('pending');
  });

  it('gets user items', function () {
    $otherUser = User::factory()->create();
    Item::factory()->create([
      'user_id' => $otherUser->id,
      'category_id' => $this->category->id,
    ]);

    $result = $this->service->getUserItems($this->user->id);

    expect($result->total())->toBe(3); // 2 lost phones + 1 found wallet

    foreach ($result->items() as $item) {
      expect($item->user_id)->toBe($this->user->id);
    }
  });

  it('gets similar items', function () {
    $mainItem = Item::factory()->verified()->lost()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $similar = $this->service->getSimilarItems($mainItem);

    expect($similar)->toHaveCount(2); // The 2 lost phones

    foreach ($similar as $item) {
      expect($item->category_id)->toBe($this->category->id);
      expect($item->type)->toBe('lost');
      expect($item->id)->not->toBe($mainItem->id);
    }
  });
});

describe('ItemService Statistics and Data', function () {
  beforeEach(function () {
    Item::factory()->count(2)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);
  });

  it('gets dashboard statistics', function () {
    $stats = $this->service->getDashboardStatistics();

    expect($stats)->toHaveKey('total_items');
    expect($stats)->toHaveKey('pending_items');
    expect($stats)->toHaveKey('verified_items');
    expect($stats['total_items'])->toBe(3);
    expect($stats['pending_items'])->toBe(1);
    expect($stats['verified_items'])->toBe(2);
  });

  it('gets category statistics', function () {
    $stats = $this->service->getCategoryStatistics();

    expect($stats)->not->toBeEmpty();
    expect($stats->first())->toHaveKey('category_name');
    expect($stats->first())->toHaveKey('count');
  });

  it('gets items trend data', function () {
    $trend = $this->service->getItemsTrend(7);

    expect($trend)->not->toBeEmpty();
    expect($trend->first())->toHaveKey('date');
    expect($trend->first())->toHaveKey('count');
  });
});

describe('ItemService Item Management', function () {
  beforeEach(function () {
    $this->item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);
  });

  it('deletes an item and its images', function () {
    // Add images to the item
    $images = ItemImage::factory()->count(2)->create(['item_id' => $this->item->id]);

    // Create fake files in storage
    foreach ($images as $image) {
      Storage::disk('public')->put('items/' . $this->item->id . '/' . $image->filename, 'fake content');
    }

    $result = $this->service->deleteItem($this->item);

    expect($result)->toBeTrue();
    expect(Item::find($this->item->id))->toBeNull();
    expect(ItemImage::where('item_id', $this->item->id)->count())->toBe(0);
  });

  it('removes a specific image from an item', function () {
    $image = ItemImage::factory()->create(['item_id' => $this->item->id]);

    // Create fake file in storage
    Storage::disk('public')->put('items/' . $this->item->id . '/' . $image->filename, 'fake content');

    $result = $this->service->removeItemImage($this->item, $image->id);

    expect($result)->toBeTrue();
    expect(ItemImage::find($image->id))->toBeNull();
  });

  it('returns false when trying to remove non-existent image', function () {
    $result = $this->service->removeItemImage($this->item, 999);

    expect($result)->toBeFalse();
  });

  it('checks if user can edit item', function () {
    $pendingItem = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $verifiedItem = Item::factory()->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $otherUserItem = Item::factory()->pending()->create([
      'user_id' => User::factory()->create()->id,
      'category_id' => $this->category->id,
    ]);

    expect($this->service->canUserEditItem($pendingItem, $this->user))->toBeTrue();
    expect($this->service->canUserEditItem($verifiedItem, $this->user))->toBeFalse();
    expect($this->service->canUserEditItem($otherUserItem, $this->user))->toBeFalse();
  });

  it('gets public item by id', function () {
    $verifiedItem = Item::factory()->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $pendingItem = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $result = $this->service->getPublicItem($verifiedItem->id);
    expect($result)->not->toBeNull();
    expect($result->id)->toBe($verifiedItem->id);

    $result = $this->service->getPublicItem($pendingItem->id);
    expect($result)->toBeNull();
  });

  it('gets item with relations', function () {
    ItemImage::factory()->create(['item_id' => $this->item->id]);

    $result = $this->service->getItemWithRelations($this->item->id);

    expect($result)->not->toBeNull();
    expect($result->category)->toBeInstanceOf(Category::class);
    expect($result->user)->toBeInstanceOf(User::class);
    expect($result->images)->toHaveCount(1);
  });

  it('gets recent items', function () {
    Item::factory()->count(5)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $result = $this->service->getRecentItems(3);

    expect($result)->toHaveCount(3);
    expect($result->first())->toBeInstanceOf(Item::class);
  });

  it('gets items by category', function () {
    Item::factory()->count(2)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $result = $this->service->getItemsByCategory($this->category->id);

    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->category_id)->toBe($this->category->id);
      expect($item->status)->toBe('verified');
    }
  });

  it('gets items requiring attention', function () {
    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'created_at' => now()->subDays(10),
    ]);

    $result = $this->service->getItemsRequiringAttention(7);

    expect($result)->toHaveCount(1);
    expect($result->first()->status)->toBe('pending');
  });
});
