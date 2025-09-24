<?php

use App\Services\StatisticsService;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
  $this->statisticsService = new StatisticsService();
});

test('get overview stats returns correct counts', function () {
  // Create test data
  $category = Category::factory()->create();
  $user = User::factory()->create();

  Item::factory()->count(5)->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'status' => 'verified',
    'type' => 'lost'
  ]);

  Item::factory()->count(3)->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'status' => 'pending',
    'type' => 'found'
  ]);

  Item::factory()->count(2)->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'status' => 'resolved',
    'type' => 'lost'
  ]);

  $stats = $this->statisticsService->getOverviewStats();

  expect($stats['total_items'])->toBe(10);
  expect($stats['verified_items'])->toBe(5);
  expect($stats['pending_items'])->toBe(3);
  expect($stats['resolved_items'])->toBe(2);
  expect($stats['lost_items'])->toBe(7);
  expect($stats['found_items'])->toBe(3);
  expect($stats['total_categories'])->toBe(1);
  expect($stats['total_users'])->toBe(1);
});

test('get success rate metrics calculates correctly', function () {
  $category = Category::factory()->create();
  $user = User::factory()->create();

  // Create 10 total items
  Item::factory()->count(6)->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'status' => 'verified',
    'verified_at' => Carbon::now()->subHours(2)
  ]);

  Item::factory()->count(2)->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'status' => 'resolved',
    'verified_at' => Carbon::now()->subDays(1),
    'resolved_at' => Carbon::now()->subHours(1)
  ]);

  Item::factory()->count(2)->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'status' => 'pending'
  ]);

  $metrics = $this->statisticsService->getSuccessRateMetrics();

  expect($metrics['verification_rate'])->toBe(80.0); // 8 verified out of 10 total
  expect($metrics['resolution_rate'])->toBe(25.0); // 2 resolved out of 8 verified
  expect($metrics['overall_success_rate'])->toBe(20.0); // 2 resolved out of 10 total
  expect($metrics['total_verified'])->toBe(8);
  expect($metrics['total_resolved'])->toBe(2);
  expect($metrics['total_submissions'])->toBe(10);
  expect($metrics['total_pending'])->toBe(2);
});

test('export statistical data includes all sections', function () {
  $category = Category::factory()->create();
  $user = User::factory()->create();

  Item::factory()->count(5)->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'status' => 'verified'
  ]);

  $exportData = $this->statisticsService->exportStatisticalData();

  expect($exportData)->toHaveKeys([
    'generated_at',
    'overview',
    'success_metrics',
    'categories',
    'submission_trends',
    'monthly_stats',
    'recent_activity'
  ]);
});
