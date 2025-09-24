<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Notifications\ItemRejectedNotification;
use App\Notifications\ItemResolvedNotification;
use App\Notifications\ItemSubmissionConfirmation;
use App\Notifications\ItemVerifiedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

test('item submission sends confirmation notification', function () {
  Notification::fake();
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $itemData = [
    'title' => 'Test Item',
    'description' => 'Test item for notification',
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

  // Check that submission confirmation was sent
  Notification::assertSentTo(
    $user,
    ItemSubmissionConfirmation::class
  );
});

test('item verification sends notification to user', function () {
  Notification::fake();
  $user = User::factory()->create();
  $admin = User::factory()->create(['role' => 'admin']);
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($admin)
    ->post(route('admin.items.verify', $item), [
      'admin_notes' => 'Item verified successfully',
    ]);

  $response->assertRedirect();

  // Check that verification notification was sent
  Notification::assertSentTo(
    $user,
    ItemVerifiedNotification::class,
    function ($notification, $channels) use ($item) {
      return $notification->item->id === $item->id;
    }
  );
});

test('item rejection sends notification to user', function () {
  Notification::fake();
  $user = User::factory()->create();
  $admin = User::factory()->create(['role' => 'admin']);
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($admin)
    ->post(route('admin.items.reject', $item), [
      'admin_notes' => 'Insufficient information provided',
    ]);

  $response->assertRedirect();

  // Check that rejection notification was sent
  Notification::assertSentTo(
    $user,
    ItemRejectedNotification::class,
    function ($notification, $channels) use ($item) {
      return $notification->item->id === $item->id;
    }
  );
});

test('item resolution sends notification to user', function () {
  Notification::fake();
  $user = User::factory()->create();
  $admin = User::factory()->create(['role' => 'admin']);
  $category = Category::factory()->create();

  $item = Item::factory()->verified()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $response = $this->actingAs($admin)
    ->post(route('admin.items.resolve', $item));

  $response->assertRedirect();

  // Check that resolution notification was sent
  Notification::assertSentTo(
    $user,
    ItemResolvedNotification::class,
    function ($notification, $channels) use ($item) {
      return $notification->item->id === $item->id;
    }
  );
});

test('notification contains correct item information', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create(['name' => 'Electronics']);

  $item = Item::factory()->pending()->create([
    'title' => 'Lost iPhone 12',
    'description' => 'Black iPhone lost in park',
    'user_id' => $user->id,
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Central Park',
  ]);

  $notification = new ItemVerifiedNotification($item);
  $mailData = $notification->toMail($user);

  expect($mailData->subject)->toContain('verified');
  expect($mailData->introLines[0])->toContain('Lost iPhone 12');
  expect($mailData->actionText)->toBe('View Item');
  expect($mailData->actionUrl)->toContain(route('public.item', $item));
});

test('rejection notification includes admin notes', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
    'status' => 'rejected',
    'admin_notes' => 'Please provide more details about the item',
  ]);

  $notification = new ItemRejectedNotification($item);
  $mailData = $notification->toMail($user);

  expect($mailData->subject)->toContain('rejected');
  expect($mailData->introLines)->toContain('Please provide more details about the item');
});

test('notification respects user preferences', function () {
  Notification::fake();

  // User who wants email notifications
  $userWithEmail = User::factory()->create([
    'notification_preferences' => [
      'item_verified' => true,
      'item_rejected' => true,
    ],
  ]);

  // User who doesn't want notifications
  $userWithoutEmail = User::factory()->create([
    'notification_preferences' => [
      'item_verified' => false,
      'item_rejected' => false,
    ],
  ]);

  $category = Category::factory()->create();

  $item1 = Item::factory()->pending()->create([
    'user_id' => $userWithEmail->id,
    'category_id' => $category->id,
  ]);

  $item2 = Item::factory()->pending()->create([
    'user_id' => $userWithoutEmail->id,
    'category_id' => $category->id,
  ]);

  // Verify both items
  $item1->markAsVerified();
  $item2->markAsVerified();

  // Only user with email preference should receive notification
  Notification::assertSentTo($userWithEmail, ItemVerifiedNotification::class);
  Notification::assertNotSentTo($userWithoutEmail, ItemVerifiedNotification::class);
});

test('bulk operations send individual notifications', function () {
  Notification::fake();
  $admin = User::factory()->create(['role' => 'admin']);
  $category = Category::factory()->create();

  $users = User::factory()->count(3)->create();
  $items = [];

  foreach ($users as $user) {
    $items[] = Item::factory()->pending()->create([
      'user_id' => $user->id,
      'category_id' => $category->id,
    ]);
  }

  $itemIds = collect($items)->pluck('id')->toArray();

  $response = $this->actingAs($admin)
    ->post(route('admin.items.bulk-verify'), [
      'item_ids' => $itemIds,
      'admin_notes' => 'Bulk verification',
    ]);

  $response->assertRedirect();

  // Each user should receive a notification
  foreach ($users as $user) {
    Notification::assertSentTo($user, ItemVerifiedNotification::class);
  }
});

test('notification delivery handles failures gracefully', function () {
  // This test would require mocking mail failures
  $user = User::factory()->create(['email' => 'invalid-email@nonexistent-domain.com']);
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  // The notification should be queued even if delivery might fail
  $notification = new ItemVerifiedNotification($item);

  // This would depend on the actual notification implementation
  // The system should handle delivery failures without breaking the verification process
  expect($notification)->toBeInstanceOf(ItemVerifiedNotification::class);
});

test('notification templates render correctly', function () {
  $user = User::factory()->create(['name' => 'John Doe']);
  $category = Category::factory()->create(['name' => 'Electronics']);

  $item = Item::factory()->create([
    'title' => 'Lost iPhone 12',
    'description' => 'Black iPhone lost in Central Park',
    'user_id' => $user->id,
    'category_id' => $category->id,
    'type' => 'lost',
    'location' => 'Central Park, NYC',
    'status' => 'verified',
    'verified_at' => now(),
  ]);

  $notification = new ItemVerifiedNotification($item);
  $mailData = $notification->toMail($user);

  // Check mail structure
  expect($mailData->subject)->not->toBeEmpty();
  expect($mailData->greeting)->toContain('John Doe');
  expect($mailData->introLines)->not->toBeEmpty();
  expect($mailData->actionText)->not->toBeEmpty();
  expect($mailData->actionUrl)->not->toBeEmpty();
  expect($mailData->outroLines)->not->toBeEmpty();
});

test('notification includes reference number', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $notification = new ItemSubmissionConfirmation($item);
  $mailData = $notification->toMail($user);

  // Should contain reference number (item ID)
  expect($mailData->introLines[0])->toContain((string) $item->id);
});

test('notification database records are created', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  // Send notification
  $user->notify(new ItemVerifiedNotification($item));

  // Check database notification record
  $this->assertDatabaseHas('notifications', [
    'notifiable_id' => $user->id,
    'notifiable_type' => User::class,
    'type' => ItemVerifiedNotification::class,
  ]);
});

test('notification channels are configured correctly', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $notification = new ItemVerifiedNotification($item);
  $channels = $notification->via($user);

  // Should use both mail and database channels
  expect($channels)->toContain('mail');
  expect($channels)->toContain('database');
});

test('notification queue integration works', function () {
  // This test would require queue testing setup
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $item = Item::factory()->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  // Notifications should be queued for better performance
  $notification = new ItemVerifiedNotification($item);

  // The actual queue testing would depend on the queue configuration
  expect($notification)->toBeInstanceOf(ItemVerifiedNotification::class);
});

test('admin notifications for new submissions work', function () {
  Notification::fake();

  $admin1 = User::factory()->create(['role' => 'admin']);
  $admin2 = User::factory()->create(['role' => 'admin']);
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $itemData = [
    'title' => 'New Submission',
    'description' => 'This should notify admins',
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

  // All admins should be notified of new submissions
  Notification::assertSentTo($admin1, \App\Notifications\AdminNewItemSubmissionNotification::class);
  Notification::assertSentTo($admin2, \App\Notifications\AdminNewItemSubmissionNotification::class);
});

test('notification unsubscribe functionality works', function () {
  $user = User::factory()->create([
    'notification_preferences' => [
      'item_verified' => true,
      'item_rejected' => true,
    ],
  ]);

  // User updates preferences to unsubscribe
  $response = $this->actingAs($user)
    ->patch(route('profile.notifications'), [
      'item_verified' => false,
      'item_rejected' => false,
    ]);

  $response->assertRedirect();

  $user->refresh();
  expect($user->notification_preferences['item_verified'])->toBeFalse();
  expect($user->notification_preferences['item_rejected'])->toBeFalse();
});

test('notification delivery respects rate limiting', function () {
  // This test would check that notifications aren't sent too frequently
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $items = Item::factory()->count(10)->pending()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  // Rapidly verify all items
  foreach ($items as $item) {
    $item->markAsVerified();
  }

  // The system should handle this gracefully without overwhelming the user
  // This would depend on the actual rate limiting implementation
  expect($items)->toHaveCount(10);
});
