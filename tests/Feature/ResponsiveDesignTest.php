<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

test('landing page renders with responsive elements', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Check for responsive meta tag
  $response->assertSee('<meta name="viewport" content="width=device-width, initial-scale=1">', false);

  // Check for responsive CSS classes (assuming Tailwind CSS)
  $response->assertSee('sm:', false); // Small screen classes
  $response->assertSee('md:', false); // Medium screen classes
  $response->assertSee('lg:', false); // Large screen classes
});

test('search interface includes mobile-friendly elements', function () {
  $response = $this->get(route('public.search'));

  $response->assertOk();

  // Should include mobile-friendly form elements
  $response->assertSee('type="search"', false);
  $response->assertSee('placeholder=', false);

  // Should have responsive layout classes
  $response->assertSee('flex', false);
  $response->assertSee('grid', false);
});

test('item cards have responsive layout', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->count(3)->verified()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->get(route('public.search'));

  $response->assertOk();

  // Should have responsive grid classes
  $response->assertSee('grid-cols-', false);
  $response->assertSee('sm:grid-cols-', false);
  $response->assertSee('md:grid-cols-', false);
  $response->assertSee('lg:grid-cols-', false);
});

test('navigation menu has mobile toggle', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Should have mobile menu toggle button
  $response->assertSee('menu-toggle', false);
  $response->assertSee('hamburger', false);

  // Should have hidden/show classes for mobile
  $response->assertSee('hidden', false);
  $response->assertSee('block', false);
});

test('forms are mobile-friendly', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)
    ->get(route('items.create'));

  $response->assertOk();

  // Form inputs should have appropriate sizing
  $response->assertSee('w-full', false);
  $response->assertSee('px-', false);
  $response->assertSee('py-', false);

  // Should have responsive spacing
  $response->assertSee('space-y-', false);
  $response->assertSee('mb-', false);
});

test('admin dashboard is tablet-friendly', function () {
  $admin = User::factory()->create(['role' => 'admin']);

  $response = $this->actingAs($admin)
    ->get(route('admin.dashboard'));

  $response->assertOk();

  // Should have responsive dashboard layout
  $response->assertSee('dashboard', false);
  $response->assertSee('grid', false);
  $response->assertSee('col-span-', false);
});

test('images are responsive', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->verified()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->get(route('public.item', $item));

  $response->assertOk();

  // Images should have responsive classes
  $response->assertSee('max-w-', false);
  $response->assertSee('w-full', false);
  $response->assertSee('h-auto', false);
});

test('tables are responsive on mobile', function () {
  $admin = User::factory()->create(['role' => 'admin']);
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Item::factory()->count(5)->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($admin)
    ->get(route('admin.pending-items'));

  $response->assertOk();

  // Should have responsive table classes
  $response->assertSee('overflow-x-auto', false);
  $response->assertSee('table', false);

  // Should have mobile-friendly alternatives
  $response->assertSee('hidden', false);
  $response->assertSee('sm:table-cell', false);
});

test('buttons are touch-friendly', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Buttons should have adequate padding for touch
  $response->assertSee('px-4', false);
  $response->assertSee('py-2', false);
  $response->assertSee('btn', false);
});

test('text is readable on mobile', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Should have appropriate text sizing
  $response->assertSee('text-sm', false);
  $response->assertSee('text-base', false);
  $response->assertSee('text-lg', false);

  // Should have responsive text classes
  $response->assertSee('sm:text-', false);
  $response->assertSee('md:text-', false);
});

test('green theme is consistent across breakpoints', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Should have green theme classes
  $response->assertSee('green-', false);
  $response->assertSee('emerald-', false);

  // Theme should be consistent across breakpoints
  $response->assertSee('bg-green-', false);
  $response->assertSee('text-green-', false);
  $response->assertSee('border-green-', false);
});

test('spacing is consistent on different screen sizes', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Should have responsive spacing classes
  $response->assertSee('p-4', false);
  $response->assertSee('m-4', false);
  $response->assertSee('space-', false);

  // Should have breakpoint-specific spacing
  $response->assertSee('sm:p-', false);
  $response->assertSee('md:p-', false);
  $response->assertSee('lg:p-', false);
});

test('search filters are accessible on mobile', function () {
  $response = $this->get(route('public.search'));

  $response->assertOk();

  // Filters should be accessible on mobile
  $response->assertSee('filter', false);

  // Should have mobile-friendly filter layout
  $response->assertSee('flex-col', false);
  $response->assertSee('sm:flex-row', false);
});

test('pagination is mobile-friendly', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  // Create enough items to trigger pagination
  Item::factory()->count(20)->verified()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->get(route('public.search'));

  $response->assertOk();

  // Should have pagination
  $response->assertSee('pagination', false);

  // Pagination should be responsive
  $response->assertSee('flex', false);
  $response->assertSee('justify-', false);
});

test('modal dialogs are responsive', function () {
  $admin = User::factory()->create(['role' => 'admin']);
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($admin)
    ->get(route('admin.items.show', $item));

  $response->assertOk();

  // Should have modal classes
  $response->assertSee('modal', false);

  // Modal should be responsive
  $response->assertSee('max-w-', false);
  $response->assertSee('w-full', false);
});

test('form validation errors are visible on mobile', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)
    ->post(route('items.store'), []);

  $response->assertSessionHasErrors();

  // Follow redirect to see the form with errors
  $response = $this->actingAs($user)
    ->get(route('items.create'));

  $response->assertOk();

  // Error messages should be visible
  $response->assertSee('error', false);
  $response->assertSee('text-red-', false);
});

test('loading states are appropriate for mobile', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Should have loading indicators
  $response->assertSee('loading', false);
  $response->assertSee('spinner', false);
});

test('accessibility features work on mobile', function () {
  $response = $this->get('/');

  $response->assertOk();

  // Should have accessibility attributes
  $response->assertSee('aria-', false);
  $response->assertSee('role=', false);
  $response->assertSee('alt=', false);

  // Should have focus indicators
  $response->assertSee('focus:', false);
});
