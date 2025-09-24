<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

test('landing page displays search interface', function () {
  $response = $this->get('/');

  $response->assertOk();
  $response->assertViewIs('public.index');
  $response->assertSee('search', false); // Search form should be present
});

test('basic search returns results', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create(['name' => 'Electronics']);

  Item::factory()->verified()->create([
    'title' => 'Lost iPhone 12 Pro',
    'description' => 'Black iPhone 12 Pro lost in Central Park',
    'type' => 'lost',
    'category_id' => $category->id,
    'user_id' => $user->id,
    'location' => 'Central Park, NYC',
    'date_occurred' => now()->subDays(2),
  ]);

  $response = $this->get(route('public.search', ['query' => 'iPhone']));

  $response->assertOk();
  $response->assertViewIs('public.search');

  $items = $response->viewData('items');
  expect($items->total())->toBe(1);
  expect($items->first()->title)->toContain('iPhone');
});

test('search by item type works', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->count(2)->verified()->create([
    'type' => 'lost',
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  Item::factory()->verified()->create([
    'type' => 'found',
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search', ['type' => 'lost']));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->total())->toBe(2);

  foreach ($items as $item) {
    expect($item->type)->toBe('lost');
  }
});

test('search by category works', function () {
  $user = User::factory()->create();
  $electronics = Category::factory()->create(['name' => 'Electronics']);
  $clothing = Category::factory()->create(['name' => 'Clothing']);

  Item::factory()->count(2)->verified()->create([
    'category_id' => $electronics->id,
    'user_id' => $user->id,
  ]);

  Item::factory()->verified()->create([
    'category_id' => $clothing->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search', ['category_id' => $electronics->id]));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->total())->toBe(2);

  foreach ($items as $item) {
    expect($item->category_id)->toBe($electronics->id);
  }
});

test('search by location works', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->verified()->create([
    'location' => 'Central Park, NYC',
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  Item::factory()->verified()->create([
    'location' => 'Subway Station',
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search', ['location' => 'Park']));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->total())->toBeGreaterThanOrEqual(1);

  foreach ($items as $item) {
    expect($item->location)->toContain('Park');
  }
});

test('multiple search filters can be combined', function () {
  $user = User::factory()->create();
  $electronics = Category::factory()->create(['name' => 'Electronics']);

  Item::factory()->count(2)->verified()->create([
    'title' => 'Lost Phone',
    'type' => 'lost',
    'category_id' => $electronics->id,
    'user_id' => $user->id,
  ]);

  Item::factory()->verified()->create([
    'title' => 'Found Wallet',
    'type' => 'found',
    'category_id' => $electronics->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search', [
    'query' => 'lost',
    'type' => 'lost',
    'category_id' => $electronics->id,
  ]));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->total())->toBe(2);

  foreach ($items as $item) {
    expect($item->type)->toBe('lost');
    expect($item->category_id)->toBe($electronics->id);
  }
});

test('search returns empty results for non-matching query', function () {
  $response = $this->get(route('public.search', ['query' => 'nonexistent']));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->total())->toBe(0);

  $response->assertSee('No items found');
});

test('search only shows verified items', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  // Create verified items
  Item::factory()->count(3)->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  // Create pending item (should not appear)
  Item::factory()->pending()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search'));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->total())->toBe(3); // Only verified items

  foreach ($items as $item) {
    expect($item->status)->toBe('verified');
  }
});

test('individual item details can be viewed', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.item', $item));

  $response->assertOk();
  $response->assertViewIs('public.item');
  $response->assertViewHas('item', $item);
  $response->assertSee($item->title);
  $response->assertSee($item->description);
});

test('non-verified items cannot be accessed publicly', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $pendingItem = Item::factory()->pending()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.item', $pendingItem));

  $response->assertNotFound();
});

test('category browsing page works', function () {
  $user = User::factory()->create();
  $electronics = Category::factory()->create(['name' => 'Electronics']);

  Item::factory()->count(2)->verified()->create([
    'category_id' => $electronics->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.browse', ['category' => $electronics->id]));

  $response->assertOk();
  $response->assertViewIs('public.browse');

  $items = $response->viewData('items');
  expect($items->total())->toBe(2);

  foreach ($items as $item) {
    expect($item->category_id)->toBe($electronics->id);
  }
});

test('search results are paginated', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  // Create more items to test pagination
  Item::factory()->count(20)->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search', ['category_id' => $category->id]));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->hasPages())->toBeTrue();
  expect($items->perPage())->toBe(15); // Default pagination
});

test('date range filters work', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'date_occurred' => now()->subDays(1),
  ]);

  Item::factory()->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'date_occurred' => now()->subDays(5),
  ]);

  $startDate = now()->subDays(2)->format('Y-m-d');
  $endDate = now()->format('Y-m-d');

  $response = $this->get(route('public.search', [
    'start_date' => $startDate,
    'end_date' => $endDate,
  ]));

  $response->assertOk();

  $items = $response->viewData('items');
  expect($items->total())->toBe(1); // Only the recent item
});

test('search interface shows filters', function () {
  $response = $this->get(route('public.search'));

  $response->assertOk();
  $response->assertSee('Type'); // Filter by type
  $response->assertSee('Category'); // Filter by category
  $response->assertSee('Location'); // Filter by location
  $response->assertSee('Date'); // Filter by date
});

test('pagination maintains search parameters', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  // Create more items
  Item::factory()->count(20)->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search', [
    'query' => 'test',
    'category_id' => $category->id,
  ]));

  $response->assertOk();

  // Check that pagination links contain the search parameters
  $response->assertSee('query=test');
  $response->assertSee('category_id=' . $category->id);
});

test('landing page shows recent items', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->count(5)->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get('/');

  $response->assertOk();

  // Should show recent verified items
  $recentItems = $response->viewData('recentItems');
  expect($recentItems)->not->toBeEmpty();
  expect($recentItems->count())->toBeLessThanOrEqual(10);
});

test('landing page shows categories', function () {
  $electronics = Category::factory()->create(['name' => 'Electronics']);
  $clothing = Category::factory()->create(['name' => 'Clothing']);

  $response = $this->get('/');

  $response->assertOk();

  $categories = $response->viewData('categories');
  expect($categories)->toContain($electronics);
  expect($categories)->toContain($clothing);
});

test('empty search shows all verified items', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->count(3)->verified()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get(route('public.search', ['query' => '']));

  $response->assertOk();

  // Should show all verified items when no query
  $items = $response->viewData('items');
  expect($items->total())->toBe(3);
});

test('search input is sanitized', function () {
  $maliciousQuery = '<script>alert("xss")</script>';

  $response = $this->get(route('public.search', ['query' => $maliciousQuery]));

  $response->assertOk();

  // Should not contain the script tag in the response
  $response->assertDontSee('<script>', false);
  $response->assertDontSee('alert("xss")', false);
});

test('search suggestions work for partial queries', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->verified()->create([
    'title' => 'iPhone 12',
    'category_id' => $category->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get('/api/search/suggestions?q=iph');

  $response->assertOk();
  $response->assertJsonStructure(['suggestions']);

  $suggestions = $response->json('suggestions');
  expect($suggestions)->toContain('iphone');
});

test('search statistics are available', function () {
  $user = User::factory()->create();
  $electronics = Category::factory()->create(['name' => 'Electronics']);
  $clothing = Category::factory()->create(['name' => 'Clothing']);

  Item::factory()->count(2)->verified()->create([
    'category_id' => $electronics->id,
    'user_id' => $user->id,
  ]);

  Item::factory()->verified()->create([
    'category_id' => $clothing->id,
    'user_id' => $user->id,
  ]);

  $response = $this->get('/api/search/stats');

  $response->assertOk();
  $response->assertJsonStructure([
    'total_searchable_items',
    'total_categories',
    'items_by_type',
    'items_by_category',
  ]);

  $stats = $response->json();
  expect($stats['total_searchable_items'])->toBe(3);
  expect($stats['total_categories'])->toBe(2);
});
