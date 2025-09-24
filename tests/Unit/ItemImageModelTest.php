<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->category = Category::factory()->create();
  $this->item = Item::factory()->create([
    'user_id' => $this->user->id,
    'category_id' => $this->category->id,
  ]);
});

describe('ItemImage Model Relationships', function () {
  it('belongs to an item', function () {
    $image = ItemImage::factory()->create(['item_id' => $this->item->id]);

    expect($image->item)->toBeInstanceOf(Item::class);
    expect($image->item->id)->toBe($this->item->id);
  });

  it('item has many images', function () {
    ItemImage::factory()->count(3)->create(['item_id' => $this->item->id]);

    expect($this->item->images)->toHaveCount(3);
    expect($this->item->images->first())->toBeInstanceOf(ItemImage::class);
  });

  it('can access item through relationship', function () {
    $image = ItemImage::factory()->create(['item_id' => $this->item->id]);

    expect($image->item->title)->toBe($this->item->title);
    expect($image->item->user_id)->toBe($this->user->id);
  });
});

describe('ItemImage Model Attributes', function () {
  it('has fillable attributes', function () {
    $imageData = [
      'item_id' => $this->item->id,
      'filename' => 'test-image.jpg',
      'original_name' => 'My Photo.jpg',
      'mime_type' => 'image/jpeg',
      'size' => 1024000,
    ];

    $image = ItemImage::create($imageData);

    expect($image->item_id)->toBe($this->item->id);
    expect($image->filename)->toBe('test-image.jpg');
    expect($image->original_name)->toBe('My Photo.jpg');
    expect($image->mime_type)->toBe('image/jpeg');
    expect($image->size)->toBe(1024000);
  });

  it('requires item_id', function () {
    expect(function () {
      ItemImage::create([
        'filename' => 'test.jpg',
        'original_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1000,
      ]);
    })->toThrow(\Illuminate\Database\QueryException::class);
  });

  it('has timestamps', function () {
    $image = ItemImage::factory()->create(['item_id' => $this->item->id]);

    expect($image->created_at)->not->toBeNull();
    expect($image->updated_at)->not->toBeNull();
  });
});

describe('ItemImage Model Factory', function () {
  it('can be created using factory', function () {
    $image = ItemImage::factory()->create(['item_id' => $this->item->id]);

    expect($image)->toBeInstanceOf(ItemImage::class);
    expect($image->filename)->not->toBeNull();
    expect($image->original_name)->not->toBeNull();
    expect($image->mime_type)->not->toBeNull();
    expect($image->size)->toBeGreaterThan(0);
    expect($image->exists)->toBeTrue();
  });

  it('can create multiple images using factory', function () {
    $images = ItemImage::factory()->count(5)->create(['item_id' => $this->item->id]);

    expect($images)->toHaveCount(5);
    expect(ItemImage::count())->toBe(5);
  });

  it('can override factory attributes', function () {
    $image = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'filename' => 'custom-image.png',
      'mime_type' => 'image/png',
    ]);

    expect($image->filename)->toBe('custom-image.png');
    expect($image->mime_type)->toBe('image/png');
  });
});

describe('ItemImage Model Queries', function () {
  beforeEach(function () {
    $this->otherItem = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    // Create images for first item
    ItemImage::factory()->count(2)->create([
      'item_id' => $this->item->id,
      'mime_type' => 'image/jpeg',
    ]);

    // Create images for second item
    ItemImage::factory()->count(3)->create([
      'item_id' => $this->otherItem->id,
      'mime_type' => 'image/png',
    ]);
  });

  it('can find images by item', function () {
    $images = ItemImage::where('item_id', $this->item->id)->get();

    expect($images)->toHaveCount(2);
    foreach ($images as $image) {
      expect($image->item_id)->toBe($this->item->id);
    }
  });

  it('can filter by mime type', function () {
    $jpegImages = ItemImage::where('mime_type', 'image/jpeg')->get();
    $pngImages = ItemImage::where('mime_type', 'image/png')->get();

    expect($jpegImages)->toHaveCount(2);
    expect($pngImages)->toHaveCount(3);
  });

  it('can order by size', function () {
    // Update sizes to test ordering
    ItemImage::where('item_id', $this->item->id)->first()->update(['size' => 5000]);
    ItemImage::where('item_id', $this->item->id)->skip(1)->first()->update(['size' => 1000]);

    $images = ItemImage::where('item_id', $this->item->id)
      ->orderBy('size', 'desc')
      ->get();

    expect($images->first()->size)->toBe(5000);
    expect($images->last()->size)->toBe(1000);
  });

  it('can find by filename', function () {
    $image = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'filename' => 'unique-filename.jpg',
    ]);

    $found = ItemImage::where('filename', 'unique-filename.jpg')->first();

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($image->id);
  });
});

describe('ItemImage Model Validation', function () {
  it('validates file size constraints', function () {
    $largeImage = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'size' => 5000000, // 5MB
    ]);

    $smallImage = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'size' => 1000, // 1KB
    ]);

    expect($largeImage->size)->toBe(5000000);
    expect($smallImage->size)->toBe(1000);
  });

  it('handles different image formats', function () {
    $formats = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    foreach ($formats as $format) {
      $image = ItemImage::factory()->create([
        'item_id' => $this->item->id,
        'mime_type' => $format,
      ]);

      expect($image->mime_type)->toBe($format);
    }
  });

  it('stores original filename correctly', function () {
    $originalNames = [
      'My Photo.jpg',
      'scan_document_2023.png',
      'IMG_20231201_123456.jpeg',
      'screenshot (1).png',
    ];

    foreach ($originalNames as $name) {
      $image = ItemImage::factory()->create([
        'item_id' => $this->item->id,
        'original_name' => $name,
      ]);

      expect($image->original_name)->toBe($name);
    }
  });
});

describe('ItemImage Model Deletion', function () {
  it('can be deleted individually', function () {
    $image = ItemImage::factory()->create(['item_id' => $this->item->id]);
    $imageId = $image->id;

    $result = $image->delete();

    expect($result)->toBeTrue();
    expect(ItemImage::find($imageId))->toBeNull();
  });

  it('is deleted when parent item is deleted', function () {
    $images = ItemImage::factory()->count(3)->create(['item_id' => $this->item->id]);
    $imageIds = $images->pluck('id')->toArray();

    $this->item->delete();

    foreach ($imageIds as $imageId) {
      expect(ItemImage::find($imageId))->toBeNull();
    }
  });

  it('does not affect other items when deleted', function () {
    $otherItem = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $image1 = ItemImage::factory()->create(['item_id' => $this->item->id]);
    $image2 = ItemImage::factory()->create(['item_id' => $otherItem->id]);

    $image1->delete();

    expect(ItemImage::find($image1->id))->toBeNull();
    expect(ItemImage::find($image2->id))->not->toBeNull();
  });
});

describe('ItemImage Model Scopes and Helpers', function () {
  it('can get file extension from filename', function () {
    $image = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'filename' => 'test-image.jpg',
    ]);

    $extension = pathinfo($image->filename, PATHINFO_EXTENSION);
    expect($extension)->toBe('jpg');
  });

  it('can determine if image is large', function () {
    $largeImage = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'size' => 2000000, // 2MB
    ]);

    $smallImage = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'size' => 500000, // 500KB
    ]);

    expect($largeImage->size > 1000000)->toBeTrue(); // > 1MB
    expect($smallImage->size > 1000000)->toBeFalse(); // < 1MB
  });

  it('can format file size for display', function () {
    $image = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'size' => 1536000, // 1.5MB
    ]);

    $sizeInMB = round($image->size / 1024 / 1024, 2);
    expect($sizeInMB)->toBe(1.46);
  });

  it('can check if image is a specific format', function () {
    $jpegImage = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'mime_type' => 'image/jpeg',
    ]);

    $pngImage = ItemImage::factory()->create([
      'item_id' => $this->item->id,
      'mime_type' => 'image/png',
    ]);

    expect($jpegImage->mime_type === 'image/jpeg')->toBeTrue();
    expect($jpegImage->mime_type === 'image/png')->toBeFalse();
    expect($pngImage->mime_type === 'image/png')->toBeTrue();
  });
});

describe('ItemImage Model Bulk Operations', function () {
  it('can delete multiple images at once', function () {
    $images = ItemImage::factory()->count(5)->create(['item_id' => $this->item->id]);
    $imageIds = $images->pluck('id')->toArray();

    $deletedCount = ItemImage::whereIn('id', $imageIds)->delete();

    expect($deletedCount)->toBe(5);
    expect(ItemImage::whereIn('id', $imageIds)->count())->toBe(0);
  });

  it('can update multiple images at once', function () {
    ItemImage::factory()->count(3)->create([
      'item_id' => $this->item->id,
      'mime_type' => 'image/jpeg',
    ]);

    $updatedCount = ItemImage::where('item_id', $this->item->id)
      ->update(['mime_type' => 'image/png']);

    expect($updatedCount)->toBe(3);

    $images = ItemImage::where('item_id', $this->item->id)->get();
    foreach ($images as $image) {
      expect($image->mime_type)->toBe('image/png');
    }
  });

  it('can count images per item', function () {
    ItemImage::factory()->count(2)->create(['item_id' => $this->item->id]);

    $otherItem = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);
    ItemImage::factory()->count(4)->create(['item_id' => $otherItem->id]);

    $item1Count = ItemImage::where('item_id', $this->item->id)->count();
    $item2Count = ItemImage::where('item_id', $otherItem->id)->count();

    expect($item1Count)->toBe(2);
    expect($item2Count)->toBe(4);
  });
});
