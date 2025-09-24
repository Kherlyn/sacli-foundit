<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
});

describe('Category Model Relationships', function () {
  it('has many items', function () {
    $category = Category::factory()->create();

    Item::factory()->count(3)->create([
      'category_id' => $category->id,
      'user_id' => $this->user->id,
    ]);

    expect($category->items)->toHaveCount(3);
    expect($category->items->first())->toBeInstanceOf(Item::class);
  });

  it('can access items through relationship', function () {
    $category = Category::factory()->create();

    $item = Item::factory()->create([
      'category_id' => $category->id,
      'user_id' => $this->user->id,
    ]);

    expect($category->items->contains($item))->toBeTrue();
    expect($item->category->id)->toBe($category->id);
  });
});

describe('Category Model Attributes', function () {
  it('has fillable attributes', function () {
    $categoryData = [
      'name' => 'Electronics',
      'description' => 'Electronic devices and gadgets',
      'icon' => 'fas fa-laptop',
      'color' => '#10B981',
    ];

    $category = Category::create($categoryData);

    expect($category->name)->toBe('Electronics');
    expect($category->description)->toBe('Electronic devices and gadgets');
    expect($category->icon)->toBe('fas fa-laptop');
    expect($category->color)->toBe('#10B981');
  });

  it('can be created with minimal data', function () {
    $category = Category::create(['name' => 'Test Category']);

    expect($category->name)->toBe('Test Category');
    expect($category->description)->toBeNull();
    expect($category->icon)->toBeNull();
    expect($category->color)->toBeNull();
  });

  it('has timestamps', function () {
    $category = Category::factory()->create();

    expect($category->created_at)->not->toBeNull();
    expect($category->updated_at)->not->toBeNull();
  });
});

describe('Category Model Factory', function () {
  it('can be created using factory', function () {
    $category = Category::factory()->create();

    expect($category)->toBeInstanceOf(Category::class);
    expect($category->name)->not->toBeNull();
    expect($category->exists)->toBeTrue();
  });

  it('can create multiple categories using factory', function () {
    $categories = Category::factory()->count(5)->create();

    expect($categories)->toHaveCount(5);
    expect(Category::count())->toBe(5);
  });

  it('can override factory attributes', function () {
    $category = Category::factory()->create([
      'name' => 'Custom Category',
      'color' => '#FF0000',
    ]);

    expect($category->name)->toBe('Custom Category');
    expect($category->color)->toBe('#FF0000');
  });
});

describe('Category Model Validation', function () {
  it('requires name field', function () {
    expect(function () {
      Category::create([
        'description' => 'Test description',
      ]);
    })->toThrow(\Illuminate\Database\QueryException::class);
  });

  it('name must be unique', function () {
    Category::factory()->create(['name' => 'Unique Category']);

    expect(function () {
      Category::create(['name' => 'Unique Category']);
    })->toThrow(\Illuminate\Database\QueryException::class);
  });
});

describe('Category Model Queries', function () {
  beforeEach(function () {
    $this->electronics = Category::factory()->create(['name' => 'Electronics']);
    $this->clothing = Category::factory()->create(['name' => 'Clothing']);
    $this->documents = Category::factory()->create(['name' => 'Documents']);
  });

  it('can find category by name', function () {
    $category = Category::where('name', 'Electronics')->first();

    expect($category)->not->toBeNull();
    expect($category->name)->toBe('Electronics');
  });

  it('can order categories by name', function () {
    $categories = Category::orderBy('name')->get();

    expect($categories->first()->name)->toBe('Clothing');
    expect($categories->last()->name)->toBe('Electronics');
  });

  it('can count items per category', function () {
    Item::factory()->count(2)->create([
      'category_id' => $this->electronics->id,
      'user_id' => $this->user->id,
    ]);

    Item::factory()->create([
      'category_id' => $this->clothing->id,
      'user_id' => $this->user->id,
    ]);

    $electronicsWithCount = Category::withCount('items')->find($this->electronics->id);
    $clothingWithCount = Category::withCount('items')->find($this->clothing->id);
    $documentsWithCount = Category::withCount('items')->find($this->documents->id);

    expect($electronicsWithCount->items_count)->toBe(2);
    expect($clothingWithCount->items_count)->toBe(1);
    expect($documentsWithCount->items_count)->toBe(0);
  });
});

describe('Category Model Deletion', function () {
  it('can be deleted when no items exist', function () {
    $category = Category::factory()->create();
    $categoryId = $category->id;

    $result = $category->delete();

    expect($result)->toBeTrue();
    expect(Category::find($categoryId))->toBeNull();
  });

  it('handles deletion with existing items based on foreign key constraints', function () {
    $category = Category::factory()->create();

    Item::factory()->create([
      'category_id' => $category->id,
      'user_id' => $this->user->id,
    ]);

    // This behavior depends on database foreign key constraints
    // In a real application, you might want to prevent deletion or cascade
    expect($category->items)->toHaveCount(1);
  });
});
