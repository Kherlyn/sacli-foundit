<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Item;

test('admin can access statistics page', function () {
  // Create an admin user
  $admin = User::factory()->create(['role' => 'admin']);

  // Create some test data
  $category = Category::factory()->create();
  Item::factory()->count(5)->create([
    'category_id' => $category->id,
    'user_id' => $admin->id,
    'status' => 'verified'
  ]);

  $response = $this->actingAs($admin)->get(route('admin.statistics'));

  $response->assertStatus(200);
  $response->assertViewIs('admin.statistics');
  $response->assertViewHas([
    'overviewStats',
    'categoryStats',
    'submissionTrends',
    'successMetrics',
    'performanceMetrics'
  ]);
});

test('admin can access statistics data endpoint', function () {
  $admin = User::factory()->create(['role' => 'admin']);

  $category = Category::factory()->create();
  Item::factory()->count(3)->create([
    'category_id' => $category->id,
    'user_id' => $admin->id,
    'status' => 'verified'
  ]);

  $response = $this->actingAs($admin)->get(route('admin.statistics.data', ['type' => 'overview']));

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true
  ]);
  $response->assertJsonStructure([
    'success',
    'data' => [
      'total_items',
      'verified_items',
      'pending_items'
    ]
  ]);
});

test('admin can export statistics', function () {
  $admin = User::factory()->create(['role' => 'admin']);

  $category = Category::factory()->create();
  Item::factory()->count(2)->create([
    'category_id' => $category->id,
    'user_id' => $admin->id,
    'status' => 'verified'
  ]);

  // Test JSON export
  $response = $this->actingAs($admin)->get(route('admin.statistics.export', ['format' => 'json']));
  $response->assertStatus(200);
  $response->assertJsonStructure([
    'generated_at',
    'overview',
    'success_metrics'
  ]);

  // Test CSV export
  $response = $this->actingAs($admin)->get(route('admin.statistics.export', ['format' => 'csv']));
  $response->assertStatus(200);
  $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('non-admin cannot access statistics', function () {
  $user = User::factory()->create(['role' => 'user']);

  $response = $this->actingAs($user)->get(route('admin.statistics'));
  $response->assertStatus(403);

  $response = $this->actingAs($user)->get(route('admin.statistics.data'));
  $response->assertStatus(403);

  $response = $this->actingAs($user)->get(route('admin.statistics.export'));
  $response->assertStatus(403);
});
