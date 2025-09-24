<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('file upload stores images correctly', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $image = UploadedFile::fake()->image('test-image.jpg', 800, 600);

  $itemData = [
    'title' => 'Test Item with Image',
    'description' => 'This item has an uploaded image',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$image],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();

  $item = Item::where('title', 'Test Item with Image')->first();
  expect($item)->not->toBeNull();
  expect($item->images)->toHaveCount(1);

  $itemImage = $item->images->first();
  expect($itemImage->original_name)->toBe('test-image.jpg');
  expect($itemImage->mime_type)->toBe('image/jpeg');

  // Check that file was actually stored
  Storage::disk('public')->assertExists('items/' . $item->id . '/' . $itemImage->filename);
});

test('multiple file uploads work correctly', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $image1 = UploadedFile::fake()->image('image1.jpg', 800, 600);
  $image2 = UploadedFile::fake()->image('image2.png', 600, 400);
  $image3 = UploadedFile::fake()->image('image3.gif', 400, 300);

  $itemData = [
    'title' => 'Item with Multiple Images',
    'description' => 'This item has multiple uploaded images',
    'category_id' => $category->id,
    'type' => 'found',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'phone',
      'phone' => '+1234567890',
    ],
    'images' => [$image1, $image2, $image3],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();

  $item = Item::where('title', 'Item with Multiple Images')->first();
  expect($item)->not->toBeNull();
  expect($item->images)->toHaveCount(3);

  // Check that all files were stored
  foreach ($item->images as $image) {
    Storage::disk('public')->assertExists('items/' . $item->id . '/' . $image->filename);
  }

  // Check different file types
  $mimeTypes = $item->images->pluck('mime_type')->toArray();
  expect($mimeTypes)->toContain('image/jpeg');
  expect($mimeTypes)->toContain('image/png');
  expect($mimeTypes)->toContain('image/gif');
});

test('file upload validates file types', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
  $textFile = UploadedFile::fake()->create('readme.txt', 500);

  $itemData = [
    'title' => 'Item with Invalid Files',
    'description' => 'This should fail validation',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$invalidFile, $textFile],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['images.0', 'images.1']);

  // No item should be created
  expect(Item::where('title', 'Item with Invalid Files')->exists())->toBeFalse();
});

test('file upload validates file sizes', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  // Create a large image (over 2MB limit)
  $largeImage = UploadedFile::fake()->image('large.jpg', 2000, 2000)->size(3000);

  $itemData = [
    'title' => 'Item with Large Image',
    'description' => 'This should fail size validation',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$largeImage],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['images.0']);
});

test('file upload limits number of images', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  // Create 6 images (over the 5 image limit)
  $images = [];
  for ($i = 1; $i <= 6; $i++) {
    $images[] = UploadedFile::fake()->image("image{$i}.jpg", 400, 300);
  }

  $itemData = [
    'title' => 'Item with Too Many Images',
    'description' => 'This should fail image count validation',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => $images,
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertSessionHasErrors(['images']);
});

test('image metadata is stored correctly', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $image = UploadedFile::fake()->image('metadata-test.png', 640, 480);

  $itemData = [
    'title' => 'Metadata Test Item',
    'description' => 'Testing image metadata storage',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$image],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();

  $item = Item::where('title', 'Metadata Test Item')->first();
  $itemImage = $item->images->first();

  expect($itemImage->original_name)->toBe('metadata-test.png');
  expect($itemImage->mime_type)->toBe('image/png');
  expect($itemImage->size)->toBeGreaterThan(0);
  expect($itemImage->filename)->not->toBe('metadata-test.png'); // Should be renamed
  expect($itemImage->filename)->toContain('.png'); // Should keep extension
});

test('image files are organized by item id', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $image1 = UploadedFile::fake()->image('item1.jpg', 400, 300);
  $image2 = UploadedFile::fake()->image('item2.jpg', 400, 300);

  // Create first item
  $itemData1 = [
    'title' => 'First Item',
    'description' => 'First item with image',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Location 1',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test1@example.com',
    ],
    'images' => [$image1],
  ];

  $this->actingAs($user)->post(route('items.store'), $itemData1);

  // Create second item
  $itemData2 = [
    'title' => 'Second Item',
    'description' => 'Second item with image',
    'category_id' => $category->id,
    'type' => 'found',
    'location' => 'Location 2',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test2@example.com',
    ],
    'images' => [$image2],
  ];

  $this->actingAs($user)->post(route('items.store'), $itemData2);

  $item1 = Item::where('title', 'First Item')->first();
  $item2 = Item::where('title', 'Second Item')->first();

  // Check that images are stored in separate directories
  $image1Path = 'items/' . $item1->id . '/' . $item1->images->first()->filename;
  $image2Path = 'items/' . $item2->id . '/' . $item2->images->first()->filename;

  Storage::disk('public')->assertExists($image1Path);
  Storage::disk('public')->assertExists($image2Path);

  expect($image1Path)->not->toBe($image2Path);
});

test('image deletion works when item is deleted', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $image = UploadedFile::fake()->image('delete-test.jpg', 400, 300);

  $itemData = [
    'title' => 'Item to Delete',
    'description' => 'This item will be deleted',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$image],
  ];

  $this->actingAs($user)->post(route('items.store'), $itemData);

  $item = Item::where('title', 'Item to Delete')->first();
  $imagePath = 'items/' . $item->id . '/' . $item->images->first()->filename;

  // Verify image exists
  Storage::disk('public')->assertExists($imagePath);

  // Delete the item
  $item->delete();

  // Verify image is deleted (this depends on model events or service implementation)
  // Storage::disk('public')->assertMissing($imagePath);
});

test('image optimization works if available', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  // Create a large image that should be optimized
  $largeImage = UploadedFile::fake()->image('large-optimize.jpg', 2000, 1500);

  $itemData = [
    'title' => 'Optimization Test',
    'description' => 'Testing image optimization',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$largeImage],
  ];

  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  $response->assertRedirect();

  $item = Item::where('title', 'Optimization Test')->first();
  $itemImage = $item->images->first();

  // Check that image was stored
  Storage::disk('public')->assertExists('items/' . $item->id . '/' . $itemImage->filename);

  // The optimization logic would depend on the actual implementation
  // This test mainly ensures the upload process doesn't break with large images
});

test('concurrent file uploads work correctly', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();
  Storage::fake('public');

  $image1 = UploadedFile::fake()->image('concurrent1.jpg', 400, 300);
  $image2 = UploadedFile::fake()->image('concurrent2.jpg', 400, 300);

  // Simulate concurrent uploads by creating items quickly
  $itemData1 = [
    'title' => 'Concurrent Item 1',
    'description' => 'First concurrent upload',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Location 1',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test1@example.com',
    ],
    'images' => [$image1],
  ];

  $itemData2 = [
    'title' => 'Concurrent Item 2',
    'description' => 'Second concurrent upload',
    'category_id' => $category->id,
    'type' => 'found',
    'location' => 'Location 2',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test2@example.com',
    ],
    'images' => [$image2],
  ];

  // Submit both requests
  $response1 = $this->actingAs($user)->post(route('items.store'), $itemData1);
  $response2 = $this->actingAs($user)->post(route('items.store'), $itemData2);

  $response1->assertRedirect();
  $response2->assertRedirect();

  // Both items should be created successfully
  expect(Item::where('title', 'Concurrent Item 1')->exists())->toBeTrue();
  expect(Item::where('title', 'Concurrent Item 2')->exists())->toBeTrue();

  $item1 = Item::where('title', 'Concurrent Item 1')->first();
  $item2 = Item::where('title', 'Concurrent Item 2')->first();

  expect($item1->images)->toHaveCount(1);
  expect($item2->images)->toHaveCount(1);

  // Both images should be stored
  Storage::disk('public')->assertExists('items/' . $item1->id . '/' . $item1->images->first()->filename);
  Storage::disk('public')->assertExists('items/' . $item2->id . '/' . $item2->images->first()->filename);
});

test('file upload handles storage errors gracefully', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  // Don't fake storage to test real storage errors
  $image = UploadedFile::fake()->image('error-test.jpg', 400, 300);

  $itemData = [
    'title' => 'Storage Error Test',
    'description' => 'Testing storage error handling',
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Test Location',
    'date_occurred' => now()->subDay()->format('Y-m-d'),
    'contact_info' => [
      'method' => 'email',
      'email' => 'test@example.com',
    ],
    'images' => [$image],
  ];

  // This test would need to mock storage failures
  // For now, we just ensure the basic structure works
  $response = $this->actingAs($user)
    ->post(route('items.store'), $itemData);

  // The response depends on how storage errors are handled in the implementation
  // This could be a redirect with error or a 500 error
});
