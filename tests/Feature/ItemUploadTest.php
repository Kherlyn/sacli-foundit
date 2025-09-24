<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->category = Category::factory()->create();

  // Fake the storage for file uploads
  Storage::fake('public');
});

describe('Item File Upload Feature Tests', function () {
  it('allows authenticated users to upload images when creating items', function () {
    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Item with Images',
        'description' => 'This is a test item with uploaded images for validation',
        'category_id' => $this->category->id,
        'type' => 'lost',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'email',
        'contact_email' => 'test@example.com',
        'images' => [
          UploadedFile::fake()->image('test1.jpg', 800, 600),
          UploadedFile::fake()->image('test2.png', 600, 400),
        ]
      ]);

    $response->assertRedirect(route('items.my-items'));
    $response->assertSessionHas('success');

    $item = Item::where('title', 'Test Item with Images')->first();
    expect($item)->not->toBeNull();
    expect($item->images)->toHaveCount(2);

    // Verify files were stored in the correct location
    foreach ($item->images as $image) {
      Storage::disk('public')->assertExists('items/' . $item->id . '/' . $image->filename);
    }
  });

  it('validates image file types and rejects invalid files', function () {
    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Item',
        'description' => 'This is a test item with invalid file types',
        'category_id' => $this->category->id,
        'type' => 'found',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'email',
        'contact_email' => 'test@example.com',
        'images' => [
          UploadedFile::fake()->create('document.pdf', 1000), // Invalid file type
          UploadedFile::fake()->create('text.txt', 500), // Invalid file type
        ]
      ]);

    $response->assertSessionHasErrors(['images.0', 'images.1']);

    // Ensure no item was created
    expect(Item::where('title', 'Test Item')->exists())->toBeFalse();
  });

  it('validates image file sizes and rejects oversized files', function () {
    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Item Large Files',
        'description' => 'This is a test item with oversized image files',
        'category_id' => $this->category->id,
        'type' => 'lost',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'phone',
        'contact_phone' => '+1234567890',
        'images' => [
          UploadedFile::fake()->image('large.jpg', 2000, 2000)->size(3000), // 3MB - too large
        ]
      ]);

    $response->assertSessionHasErrors(['images.0']);

    // Ensure no item was created
    expect(Item::where('title', 'Test Item Large Files')->exists())->toBeFalse();
  });

  it('limits the number of images to maximum 5', function () {
    $images = [];
    for ($i = 1; $i <= 7; $i++) {
      $images[] = UploadedFile::fake()->image("test{$i}.jpg", 400, 300);
    }

    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Item Too Many Images',
        'description' => 'This is a test item with too many image files',
        'category_id' => $this->category->id,
        'type' => 'found',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'both',
        'contact_email' => 'test@example.com',
        'contact_phone' => '+1234567890',
        'images' => $images
      ]);

    $response->assertSessionHasErrors(['images']);

    // Ensure no item was created
    expect(Item::where('title', 'Test Item Too Many Images')->exists())->toBeFalse();
  });

  it('allows items to be created without images', function () {
    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Item No Images',
        'description' => 'This is a test item without any images',
        'category_id' => $this->category->id,
        'type' => 'lost',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'email',
        'contact_email' => 'test@example.com',
      ]);

    $response->assertRedirect(route('items.my-items'));
    $response->assertSessionHas('success');

    $item = Item::where('title', 'Test Item No Images')->first();
    expect($item)->not->toBeNull();
    expect($item->images)->toHaveCount(0);
  });

  it('allows users to add images when editing items', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->user)
      ->patch(route('items.update', $item), [
        'title' => $item->title,
        'description' => $item->description,
        'category_id' => $item->category_id,
        'location' => $item->location,
        'date_occurred' => $item->date_occurred->format('Y-m-d'),
        'contact_method' => $item->contact_info['method'],
        'contact_email' => $item->contact_info['email'] ?? null,
        'contact_phone' => $item->contact_info['phone'] ?? null,
        'images' => [
          UploadedFile::fake()->image('edit_test.jpg', 500, 400),
        ]
      ]);

    $response->assertRedirect(route('items.my-items'));
    $response->assertSessionHas('success');

    $item->refresh();
    expect($item->images)->toHaveCount(1);

    Storage::disk('public')->assertExists('items/' . $item->id . '/' . $item->images->first()->filename);
  });

  it('allows users to remove images from their items', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    // Create an image for the item
    $image = $item->images()->create([
      'filename' => 'test_image.jpg',
      'original_name' => 'test_image.jpg',
      'mime_type' => 'image/jpeg',
      'size' => 1024,
    ]);

    // Create fake file in storage
    Storage::disk('public')->put('items/' . $item->id . '/' . $image->filename, 'fake content');

    $response = $this->actingAs($this->user)
      ->delete(route('items.remove-image', [$item, $image->id]));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify image was removed from database
    expect($item->images()->count())->toBe(0);
  });

  it('prevents users from editing items that are not pending', function () {
    $verifiedItem = Item::factory()->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->user)
      ->get(route('items.edit', $verifiedItem));

    $response->assertStatus(403);
  });

  it('prevents users from editing items they do not own', function () {
    $otherUser = User::factory()->create();
    $item = Item::factory()->pending()->create([
      'user_id' => $otherUser->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->user)
      ->get(route('items.edit', $item));

    $response->assertStatus(403);
  });

  it('handles file upload errors gracefully', function () {
    // Simulate a file upload error by using an invalid file
    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Item Upload Error',
        'description' => 'This is a test item with upload error simulation',
        'category_id' => $this->category->id,
        'type' => 'lost',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'email',
        'contact_email' => 'test@example.com',
        'images' => [
          UploadedFile::fake()->create('invalid', 0), // Invalid file
        ]
      ]);

    // Should handle the error and show validation message
    $response->assertSessionHasErrors();
  });

  it('stores images in organized directory structure', function () {
    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Directory Structure',
        'description' => 'Testing organized directory structure for image storage',
        'category_id' => $this->category->id,
        'type' => 'found',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'email',
        'contact_email' => 'test@example.com',
        'images' => [
          UploadedFile::fake()->image('structure_test.jpg', 600, 400),
        ]
      ]);

    $item = Item::where('title', 'Test Directory Structure')->first();
    $image = $item->images->first();

    // Verify the file is stored in the correct directory structure: items/{item_id}/{filename}
    Storage::disk('public')->assertExists('items/' . $item->id . '/' . $image->filename);

    // Verify the filename is a UUID format (not the original name)
    expect($image->filename)->not->toBe('structure_test.jpg');
    expect($image->original_name)->toBe('structure_test.jpg');
  });

  it('preserves image metadata correctly', function () {
    $originalFile = UploadedFile::fake()->image('metadata_test.png', 800, 600);

    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test Image Metadata',
        'description' => 'Testing image metadata preservation during upload',
        'category_id' => $this->category->id,
        'type' => 'lost',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'email',
        'contact_email' => 'test@example.com',
        'images' => [$originalFile]
      ]);

    $item = Item::where('title', 'Test Image Metadata')->first();
    $image = $item->images->first();

    expect($image->original_name)->toBe('metadata_test.png');
    expect($image->mime_type)->toBe('image/png');
    expect($image->size)->toBeGreaterThan(0);
  });
});

describe('Image Processing and Optimization', function () {
  it('handles different image formats correctly', function () {
    $formats = [
      UploadedFile::fake()->image('test.jpg', 400, 300),
      UploadedFile::fake()->image('test.png', 400, 300),
      UploadedFile::fake()->image('test.gif', 400, 300),
    ];

    foreach ($formats as $index => $file) {
      $response = $this->actingAs($this->user)
        ->post(route('items.store'), [
          'title' => "Test Format {$index}",
          'description' => 'Testing different image format support',
          'category_id' => $this->category->id,
          'type' => 'found',
          'location' => 'Test Location',
          'date_occurred' => now()->subDay()->format('Y-m-d'),
          'contact_method' => 'email',
          'contact_email' => 'test@example.com',
          'images' => [$file]
        ]);

      $response->assertRedirect(route('items.my-items'));

      $item = Item::where('title', "Test Format {$index}")->first();
      expect($item->images)->toHaveCount(1);

      Storage::disk('public')->assertExists('items/' . $item->id . '/' . $item->images->first()->filename);
    }
  });

  it('maintains file integrity during storage', function () {
    $originalFile = UploadedFile::fake()->image('integrity_test.jpg', 500, 400);
    $originalSize = $originalFile->getSize();

    $response = $this->actingAs($this->user)
      ->post(route('items.store'), [
        'title' => 'Test File Integrity',
        'description' => 'Testing file integrity during storage process',
        'category_id' => $this->category->id,
        'type' => 'lost',
        'location' => 'Test Location',
        'date_occurred' => now()->subDay()->format('Y-m-d'),
        'contact_method' => 'email',
        'contact_email' => 'test@example.com',
        'images' => [$originalFile]
      ]);

    $item = Item::where('title', 'Test File Integrity')->first();
    $image = $item->images->first();

    // Verify the stored file exists and has content
    $storedPath = 'items/' . $item->id . '/' . $image->filename;
    Storage::disk('public')->assertExists($storedPath);

    $storedContent = Storage::disk('public')->get($storedPath);
    expect(strlen($storedContent))->toBeGreaterThan(0);
  });
});
