<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('authenticated user can submit a lost item', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create(['name' => 'Electronics']);
  Storage::fake('public');

  $itemData = [
    'title' => 'Lost iPhone 12',
    'description' => 'Black iPhone 12 Pro lost in Central Park yesterday evening',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Central Park, NYC',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();
  $response->assertSessionHas('success');

  $this->assertDatabaseHas('items', [
    'title' => 'Lost iPhone 12',
    'type' => 'lost',
    'status' => 'pending',
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);
});

test('authenticated user can submit a found item', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create(['name' => 'Electronics']);

  $itemData = [
    'title' => 'Found Wallet',
    'description' => 'Brown leather wallet found near subway station',
    'category_id' => $category->id,
    'type' => 'found',
    'location' => 'Subway Station',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'phone',
      'phone' => '+1234567890',
    ],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();
  $response->assertSessionHas('success');

  $this->assertDatabaseHas('items', [
    'title' => 'Found Wallet',
    'type' => 'found',
    'status' => 'pending',
    'user_id' => $user->id,
  ]);
});

test('user can submit item with images', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create(['name' => 'Electronics']);
  Storage::fake('public');

  $image1 = UploadedFile::fake()->image('item1.jpg', 800, 600);
  $image2 = UploadedFile::fake()->image('item2.png', 600, 400);

  $itemData = [
    'title' => 'Lost Camera',
    'description' => 'Digital camera lost at the beach',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Beach',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'both',
      'email' => 'test@example.com',
      'phone' => '+1234567890',
    ],
    'images' => [$image1, $image2],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();
  $response->assertSessionHas('success');

  $item = Item::where('title', 'Lost Camera')->first();
  expect($item)->not->toBeNull();
  expect($item->images)->toHaveCount(2);

  // Check that files were stored
  foreach ($item->images as $image) {
    Storage::disk('public')->assertExists('items/' . $item->id . '/' . $image->filename);
  }
});

test('item submission validates required fields', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)
    ->post(route('items.store'), []);

  $response->assertSessionHasErrors([
    'title',
    'description',
    'category_id',
    'type',
    'location',
    'date_occurred',
    'contact_info',
  ]);
});

test('item submission validates title length', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $itemData = [
    'title' => str_repeat('a', 300), // Too long
    'description' => 'Valid description',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['title']);
});

test('item submission validates description minimum length', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $itemData = [
    'title' => 'Valid Title',
    'description' => 'Short', // Too short
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['description']);
});

test('item submission rejects future dates', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $itemData = [
    'title' => 'Valid Title',
    'description' => 'Valid description with enough characters',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->addDay()->format('Y-m-d'), // Future date
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['date_occurred']);
});

test('item submission validates contact information', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  // Test email method without email
  $itemData = [
    'title' => 'Valid Title',
    'description' => 'Valid description with enough characters',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      // Missing email
    ],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['contact_info.email']);
});

test('item submission validates image files', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $invalidFile = UploadedFile::fake()->create('document.pdf', 1000); // Not an image
  $largeImage = UploadedFile::fake()->image('large.jpg', 2000, 2000)->size(3000); // Too large

  $itemData = [
    'title' => 'Valid Title',
    'description' => 'Valid description with enough characters',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$invalidFile, $largeImage],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['images.0', 'images.1']);
});

test('unauthenticated users cannot submit items', function () {
  $category = Category::factory()->create();

  $itemData = [
    'title' => 'Test Item',
    'description' => 'Test description',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
  ];

  $response = $this->post(route('items.store'), $itemData);

  $response->assertRedirect(route('login'));
});

test('authenticated users can view submission form', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)
    ->get(route('items.create'));

  $response->assertOk();
  $response->assertViewIs('items.create');
  $response->assertViewHas('categories');
});

test('unauthenticated users are redirected from submission form', function () {
  $response = $this->get(route('items.create'));

  $response->assertRedirect(route('login'));
});

test('user can view their own items', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  // Create items for this user
  Item::factory()->count(3)->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  // Create items for another user
  $otherUser = User::factory()->create();
  Item::factory()->count(2)->create([
    'user_id' => $otherUser->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($user)
    ->get(route('items.my-items'));

  $response->assertOk();
  $response->assertViewIs('items.my-items');

  // Should only see their own items
  $viewItems = $response->viewData('items');
  expect($viewItems->total())->toBe(3);
});

test('user can edit their pending items', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($user)
    ->get(route('items.edit', $item));

  $response->assertOk();
  $response->assertViewIs('items.edit');
  $response->assertViewHas('item', $item);
});

test('user cannot edit verified items', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->verified()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($user)
    ->get(route('items.edit', $item));

  $response->assertForbidden();
});

test('user cannot edit other users items', function () {
  $user = User::factory()->create();
  $otherUser = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $otherUser->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($user)
    ->get(route('items.edit', $item));

  $response->assertForbidden();
});

test('user can update their pending items', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $updateData = [
    'title' => 'Updated Title',
    'description' => 'Updated description with more details',
    'category_id' => $category->id,
    'type' => $item->type,
    'location' => 'Updated Location',
    'date_occurred' => $item->date_occurred->format('Y-m-d'),
    'contact_info' => $item->contact_info,
  ];

  $response = $this->actingAs($user)
    ->put(route('items.update', $item), $updateData);

  $response->assertRedirect();
  $response->assertSessionHas('success');

  $item->refresh();
  expect($item->title)->toBe('Updated Title');
  expect($item->description)->toBe('Updated description with more details');
  expect($item->location)->toBe('Updated Location');
});

test('successful submission shows confirmation with reference number', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $itemData = [
    'title' => 'Test Item',
    'description' => 'Test description with enough characters',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();
  $response->assertSessionHas('success');

  // Check that the success message contains reference number
  $successMessage = session('success');
  expect($successMessage)->toContain('reference');
});
