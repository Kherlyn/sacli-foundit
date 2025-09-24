<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
  $this->admin = User::factory()->create(['role' => 'admin']);
  $this->user = User::factory()->create(['role' => 'user']);
  $this->category = Category::factory()->create();

  Notification::fake();
});

describe('Admin Dashboard Access', function () {
  it('allows admin to access dashboard', function () {
    $response = $this->actingAs($this->admin)
      ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewIs('admin.dashboard');
  });

  it('prevents non-admin users from accessing dashboard', function () {
    $response = $this->actingAs($this->user)
      ->get(route('admin.dashboard'));

    $response->assertForbidden();
  });

  it('redirects unauthenticated users to login', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('login'));
  });

  it('shows dashboard statistics', function () {
    // Create test data
    Item::factory()->count(5)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->count(3)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.dashboard'));

    $response->assertOk();

    $stats = $response->viewData('stats');
    expect($stats['pending_items'])->toBe(5);
    expect($stats['verified_items'])->toBe(3);
    expect($stats['total_items'])->toBe(8);
  });
});

describe('Pending Items Management', function () {
  it('shows pending items queue', function () {
    $pendingItems = Item::factory()->count(3)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.pending-items'));

    $response->assertOk();
    $response->assertViewIs('admin.pending-items');

    $items = $response->viewData('items');
    expect($items->total())->toBe(3);

    foreach ($items as $item) {
      expect($item->status)->toBe('pending');
    }
  });

  it('allows admin to view individual pending item', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.items.show', $item));

    $response->assertOk();
    $response->assertViewIs('admin.items.show');
    $response->assertViewHas('item', $item);
  });

  it('shows item details with user and category information', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.items.show', $item));

    $response->assertOk();
    $response->assertSee($item->title);
    $response->assertSee($item->description);
    $response->assertSee($this->user->name);
    $response->assertSee($this->category->name);
  });

  it('filters pending items by category', function () {
    $electronics = Category::factory()->create(['name' => 'Electronics']);
    $clothing = Category::factory()->create(['name' => 'Clothing']);

    Item::factory()->count(2)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $electronics->id,
    ]);

    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $clothing->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.pending-items', ['category_id' => $electronics->id]));

    $response->assertOk();

    $items = $response->viewData('items');
    expect($items->total())->toBe(2);

    foreach ($items as $item) {
      expect($item->category_id)->toBe($electronics->id);
    }
  });

  it('searches pending items by query', function () {
    Item::factory()->pending()->create([
      'title' => 'Lost iPhone',
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->pending()->create([
      'title' => 'Found Wallet',
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.pending-items', ['query' => 'iPhone']));

    $response->assertOk();

    $items = $response->viewData('items');
    expect($items->total())->toBe(1);
    expect($items->first()->title)->toContain('iPhone');
  });
});

describe('Item Verification Process', function () {
  it('allows admin to verify an item', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.verify', $item), [
        'admin_notes' => 'Item looks legitimate and complete',
      ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $item->refresh();
    expect($item->status)->toBe('verified');
    expect($item->admin_notes)->toBe('Item looks legitimate and complete');
    expect($item->verified_at)->not->toBeNull();
  });

  it('allows admin to reject an item', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.reject', $item), [
        'admin_notes' => 'Insufficient information provided',
      ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $item->refresh();
    expect($item->status)->toBe('rejected');
    expect($item->admin_notes)->toBe('Insufficient information provided');
  });

  it('allows admin to resolve an item', function () {
    $item = Item::factory()->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.resolve', $item));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $item->refresh();
    expect($item->status)->toBe('resolved');
    expect($item->resolved_at)->not->toBeNull();
  });

  it('prevents non-admin users from verifying items', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->user)
      ->post(route('admin.items.verify', $item), [
        'admin_notes' => 'Should not work',
      ]);

    $response->assertForbidden();

    $item->refresh();
    expect($item->status)->toBe('pending');
  });

  it('validates admin notes when provided', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $longNotes = str_repeat('a', 1100); // Too long

    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.verify', $item), [
        'admin_notes' => $longNotes,
      ]);

    $response->assertSessionHasErrors(['admin_notes']);
  });

  it('sends notification to user when item is verified', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->admin)
      ->post(route('admin.items.verify', $item), [
        'admin_notes' => 'Verified successfully',
      ]);

    // Check that notification was sent
    Notification::assertSentTo(
      $this->user,
      \App\Notifications\ItemVerifiedNotification::class
    );
  });

  it('sends notification to user when item is rejected', function () {
    $item = Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->admin)
      ->post(route('admin.items.reject', $item), [
        'admin_notes' => 'Needs more information',
      ]);

    // Check that notification was sent
    Notification::assertSentTo(
      $this->user,
      \App\Notifications\ItemRejectedNotification::class
    );
  });
});

describe('Bulk Operations', function () {
  it('allows admin to bulk verify items', function () {
    $items = Item::factory()->count(3)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $itemIds = $items->pluck('id')->toArray();

    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.bulk-verify'), [
        'item_ids' => $itemIds,
        'admin_notes' => 'Bulk verification',
      ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    foreach ($items as $item) {
      $item->refresh();
      expect($item->status)->toBe('verified');
      expect($item->admin_notes)->toBe('Bulk verification');
    }
  });

  it('allows admin to bulk reject items', function () {
    $items = Item::factory()->count(3)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $itemIds = $items->pluck('id')->toArray();

    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.bulk-reject'), [
        'item_ids' => $itemIds,
        'admin_notes' => 'Bulk rejection',
      ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    foreach ($items as $item) {
      $item->refresh();
      expect($item->status)->toBe('rejected');
      expect($item->admin_notes)->toBe('Bulk rejection');
    }
  });

  it('validates bulk operation item selection', function () {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.bulk-verify'), [
        'item_ids' => [], // Empty selection
        'admin_notes' => 'Should fail',
      ]);

    $response->assertSessionHasErrors(['item_ids']);
  });

  it('prevents bulk operations on non-existent items', function () {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.items.bulk-verify'), [
        'item_ids' => [999, 1000], // Non-existent IDs
        'admin_notes' => 'Should fail',
      ]);

    $response->assertSessionHasErrors();
  });
});

describe('Admin Statistics and Reports', function () {
  it('shows verification statistics', function () {
    // Create test data
    Item::factory()->count(10)->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->count(15)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    Item::factory()->count(5)->create([
      'status' => 'rejected',
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.statistics'));

    $response->assertOk();
    $response->assertViewIs('admin.statistics');

    $stats = $response->viewData('stats');
    expect($stats['pending_items'])->toBe(10);
    expect($stats['verified_items'])->toBe(15);
    expect($stats['rejected_items'])->toBe(5);
  });

  it('shows category breakdown', function () {
    $electronics = Category::factory()->create(['name' => 'Electronics']);
    $clothing = Category::factory()->create(['name' => 'Clothing']);

    Item::factory()->count(5)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $electronics->id,
    ]);

    Item::factory()->count(3)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $clothing->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.statistics'));

    $response->assertOk();

    $categoryStats = $response->viewData('categoryStats');
    expect($categoryStats)->not->toBeEmpty();
  });

  it('allows exporting statistics data', function () {
    Item::factory()->count(5)->verified()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.statistics.export'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/json');

    $data = $response->json();
    expect($data)->toHaveKey('generated_at');
    expect($data)->toHaveKey('overview');
    expect($data)->toHaveKey('success_metrics');
  });

  it('shows items requiring attention', function () {
    // Create old pending item
    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'created_at' => now()->subDays(10),
    ]);

    // Create recent pending item
    Item::factory()->pending()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'created_at' => now()->subDays(1),
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.items.attention'));

    $response->assertOk();

    $items = $response->viewData('items');
    expect($items)->toHaveCount(1); // Only the old one
  });
});
