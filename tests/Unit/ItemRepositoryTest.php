<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use App\Repositories\ItemRepository;

beforeEach(function () {
  $this->repository = new ItemRepository();
  $this->user = User::factory()->create();
  $this->category = Category::factory()->create();
});

describe('ItemRepository Public Items', function () {
  beforeEach(function () {
    // Create items with different statuses
    Item::factory()->count(3)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->count(2)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->rejected()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);
  });

  it('finds only public (verified) items', function () {
    $result = $this->repository->findPublicItems();

    expect($result->total())->toBe(3);

    foreach ($result->items() as $item) {
      expect($item)->toBeInstanceOf(Item::class);
      expect($item->status)->toBe('verified');
    }
  });

  it('finds public item by id', function () {
    $verifiedItem = Item::factory()->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $pendingItem = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $result = $this->repository->findPublicItem($verifiedItem->id);
    expect($result)->not->toBeNull();
    expect($result->id)->toBe($verifiedItem->id);

    $result = $this->repository->findPublicItem($pendingItem->id);
    expect($result)->toBeNull();
  });

  it('gets recent items', function () {
    $result = $this->repository->getRecentItems(2);

    expect($result)->toHaveCount(2);
    expect($result->first())->toBeInstanceOf(Item::class);
    expect($result->first()->status)->toBe('verified');
  });
});

describe('ItemRepository Search Functionality', function () {
  beforeEach(function () {
    $electronics = Category::factory()->create(['name' => 'Electronics Test']);
    $clothing = Category::factory()->create(['name' => 'Clothing Test']);

    // Create searchable items
    Item::factory()->verified()->create([
      'title' => 'Lost iPhone 12',
      'description' => 'Black iPhone 12 Pro lost in Central Park',
      'type' => 'lost',
      'category_id' => $electronics->id,
      'location' => 'Central Park, NYC',
      'user_id' => $this->user->id,
      'date_occurred' => now()->subDays(2),
    ]);

    Item::factory()->verified()->create([
      'title' => 'Found Wallet',
      'description' => 'Brown leather wallet found near subway',
      'type' => 'found',
      'category_id' => $clothing->id,
      'location' => 'Subway Station',
      'user_id' => $this->user->id,
      'date_occurred' => now()->subDays(1),
    ]);

    Item::factory()->verified()->create([
      'title' => 'Lost Keys',
      'description' => 'Car keys with blue keychain',
      'type' => 'lost',
      'category_id' => $electronics->id,
      'location' => 'Office Building',
      'user_id' => $this->user->id,
      'date_occurred' => now()->subDays(3),
    ]);
  });

  it('searches items by query', function () {
    $result = $this->repository->searchItems(query: 'iPhone');
    expect($result->total())->toBe(1);
    expect($result->items()[0]->title)->toContain('iPhone');

    $result = $this->repository->searchItems(query: 'wallet');
    expect($result->total())->toBe(1);
    expect($result->items()[0]->title)->toContain('Wallet');
  });

  it('filters items by type', function () {
    $result = $this->repository->searchItems(type: 'lost');
    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->type)->toBe('lost');
    }

    $result = $this->repository->searchItems(type: 'found');
    expect($result->total())->toBe(1);
    expect($result->items()[0]->type)->toBe('found');
  });

  it('filters items by category', function () {
    $electronics = Category::where('name', 'Electronics Test')->first();

    $result = $this->repository->searchItems(categoryId: $electronics->id);
    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->category_id)->toBe($electronics->id);
    }
  });

  it('filters items by location', function () {
    $result = $this->repository->searchItems(location: 'Park');
    expect($result->total())->toBe(1);
    expect($result->items()[0]->location)->toContain('Park');
  });

  it('filters items by date range', function () {
    $result = $this->repository->searchItems(
      startDate: now()->subDays(2)->format('Y-m-d')
    );
    expect($result->total())->toBe(2);

    $result = $this->repository->searchItems(
      endDate: now()->subDays(1)->format('Y-m-d')
    );
    expect($result->total())->toBe(2);
  });

  it('combines multiple filters', function () {
    $electronics = Category::where('name', 'Electronics Test')->first();

    $result = $this->repository->searchItems(
      query: 'iPhone',
      type: 'lost',
      categoryId: $electronics->id
    );

    expect($result->total())->toBe(1);
    expect($result->items()[0]->title)->toContain('iPhone');
    expect($result->items()[0]->type)->toBe('lost');
    expect($result->items()[0]->category_id)->toBe($electronics->id);
  });
});

describe('ItemRepository Admin Functions', function () {
  beforeEach(function () {
    Item::factory()->count(2)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->count(3)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->rejected()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);
  });

  it('gets pending items', function () {
    $result = $this->repository->getPendingItems();

    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->status)->toBe('pending');
    }
  });

  it('gets items by status', function () {
    $result = $this->repository->getItemsByStatus('verified');
    expect($result->total())->toBe(3);

    $result = $this->repository->getItemsByStatus('rejected');
    expect($result->total())->toBe(1);
  });

  it('admin searches items with all statuses', function () {
    $result = $this->repository->adminSearchItems();
    expect($result->total())->toBe(6); // All items regardless of status

    $result = $this->repository->adminSearchItems(status: 'pending');
    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->status)->toBe('pending');
    }
  });

  it('gets items requiring attention', function () {
    // Create old pending item
    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'created_at' => now()->subDays(10),
    ]);

    $result = $this->repository->getItemsRequiringAttention(7);
    expect($result)->toHaveCount(1);
    expect($result->first()->status)->toBe('pending');
  });
});

describe('ItemRepository Statistics', function () {
  beforeEach(function () {
    $electronics = Category::factory()->create(['name' => 'Electronics Stats']);
    $clothing = Category::factory()->create(['name' => 'Clothing Stats']);

    // Create items for statistics
    Item::factory()->count(2)->verified()->lost()->create([
      'user_id' => $this->user->id,
      'category_id' => $electronics->id,
    ]);

    Item::factory()->verified()->found()->create([
      'user_id' => $this->user->id,
      'category_id' => $clothing->id,
    ]);

    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $electronics->id,
    ]);

    Item::factory()->rejected()->create([
      'user_id' => $this->user->id,
      'category_id' => $clothing->id,
    ]);

    Item::factory()->resolved()->create([
      'user_id' => $this->user->id,
      'category_id' => $electronics->id,
    ]);
  });

  it('gets general statistics', function () {
    $stats = $this->repository->getStatistics();

    expect($stats)->toHaveKey('total_items');
    expect($stats)->toHaveKey('pending_items');
    expect($stats)->toHaveKey('verified_items');
    expect($stats)->toHaveKey('resolved_items');
    expect($stats)->toHaveKey('rejected_items');
    expect($stats)->toHaveKey('lost_items');
    expect($stats)->toHaveKey('found_items');

    expect($stats['total_items'])->toBe(6);
    expect($stats['pending_items'])->toBe(1);
    expect($stats['verified_items'])->toBe(3);
    expect($stats['resolved_items'])->toBe(1);
    expect($stats['rejected_items'])->toBe(1);
    expect($stats['lost_items'])->toBe(2);
    expect($stats['found_items'])->toBe(1);
  });

  it('gets category statistics', function () {
    $stats = $this->repository->getCategoryStatistics();

    expect($stats)->toHaveCount(2);
    expect($stats->first())->toHaveKey('category_name');
    expect($stats->first())->toHaveKey('count');
  });

  it('gets items trend data', function () {
    $trend = $this->repository->getItemsTrend(7);

    expect($trend)->not->toBeEmpty();
    expect($trend->first())->toHaveKey('date');
    expect($trend->first())->toHaveKey('count');
  });

  it('gets items by type and status', function () {
    $data = $this->repository->getItemsByTypeAndStatus();

    expect($data)->not->toBeEmpty();
    expect($data->first())->toHaveKey('type');
    expect($data->first())->toHaveKey('status');
    expect($data->first())->toHaveKey('count');
  });
});

describe('ItemRepository User Functions', function () {
  beforeEach(function () {
    $otherUser = User::factory()->create();

    // Create items for this user
    Item::factory()->count(2)->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    // Create items for other user
    Item::factory()->create([
      'user_id' => $otherUser->id,
      'category_id' => $this->category->id,
    ]);
  });

  it('gets items by user', function () {
    $result = $this->repository->getItemsByUser($this->user->id);

    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->user_id)->toBe($this->user->id);
    }
  });

  it('gets items by category', function () {
    $result = $this->repository->getItemsByCategory($this->category->id);

    // Only verified items should be returned for public category browsing
    $verifiedCount = Item::where('category_id', $this->category->id)
      ->where('status', 'verified')
      ->count();

    expect($result->total())->toBe($verifiedCount);
  });
});

describe('ItemRepository Relationships and Similar Items', function () {
  it('finds item with relationships', function () {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    ItemImage::factory()->count(2)->create(['item_id' => $item->id]);

    $result = $this->repository->findWithRelations($item->id);

    expect($result)->not->toBeNull();
    expect($result->category)->toBeInstanceOf(Category::class);
    expect($result->user)->toBeInstanceOf(User::class);
    expect($result->images)->toHaveCount(2);
  });

  it('gets similar items', function () {
    $mainItem = Item::factory()->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'type' => 'lost',
    ]);

    // Create similar items (same category and type)
    Item::factory()->count(3)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'type' => 'lost',
    ]);

    // Create different items
    Item::factory()->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'type' => 'found', // Different type
    ]);

    $similar = $this->repository->getSimilarItems($mainItem, 5);

    expect($similar)->toHaveCount(3);

    foreach ($similar as $item) {
      expect($item->category_id)->toBe($this->category->id);
      expect($item->type)->toBe('lost');
      expect($item->id)->not->toBe($mainItem->id);
    }
  });
});
