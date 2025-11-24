<?php

use App\Models\Admin;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

describe('Admin Dashboard', function () {
  it('displays dashboard for authenticated admin', function () {
    $admin = Admin::factory()->create();

    // Create some test data
    $category = Category::factory()->create();
    $user = User::factory()->create();
    Item::factory()->count(5)->create([
      'category_id' => $category->id,
      'user_id' => $user->id,
    ]);

    $response = $this->actingAs($admin, 'admin')
      ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewIs('admin.dashboard');
    $response->assertViewHas('statistics');
    $response->assertViewHas('categoryStats');
    $response->assertViewHas('recentItems');
    $response->assertViewHas('pendingCount');
  });

  it('redirects unauthenticated users to login', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
  });

  it('shows correct statistics on dashboard', function () {
    $admin = Admin::factory()->create();
    $category = Category::factory()->create();
    $user = User::factory()->create();

    // Create items with different statuses
    Item::factory()->count(3)->create([
      'category_id' => $category->id,
      'user_id' => $user->id,
      'status' => 'pending',
    ]);

    Item::factory()->count(5)->create([
      'category_id' => $category->id,
      'user_id' => $user->id,
      'status' => 'verified',
    ]);

    $response = $this->actingAs($admin, 'admin')
      ->get(route('admin.dashboard'));

    $response->assertOk();

    // Verify statistics are passed to view
    $statistics = $response->viewData('statistics');
    expect($statistics)->toHaveKey('total_items');
    expect($statistics)->toHaveKey('pending_items');
    expect($statistics)->toHaveKey('verified_items');

    // Verify pending count
    $pendingCount = $response->viewData('pendingCount');
    expect($pendingCount)->toBe(3);
  });
});
