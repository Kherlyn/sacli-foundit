<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Services\NotificationService;
use App\Notifications\ItemVerifiedNotification;
use App\Notifications\ItemRejectedNotification;
use App\Notifications\ItemResolvedNotification;
use App\Notifications\ItemSubmissionConfirmation;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->notificationService = new NotificationService();
    $this->user = User::factory()->create([
        'notification_preferences' => [
            'item_verified' => true,
            'item_rejected' => true,
            'item_resolved' => false,
        ]
    ]);
    $this->category = Category::factory()->create();
    $this->item = Item::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
    ]);
});

test('sends item verified notification when user wants it', function () {
    Notification::fake();

    $this->notificationService->sendItemVerifiedNotification($this->item);

    Notification::assertSentTo(
        $this->user,
        ItemVerifiedNotification::class,
        function ($notification) {
            return $notification->item->id === $this->item->id;
        }
    );
});

test('sends item rejected notification when user wants it', function () {
    Notification::fake();

    $this->notificationService->sendItemRejectedNotification($this->item);

    Notification::assertSentTo(
        $this->user,
        ItemRejectedNotification::class,
        function ($notification) {
            return $notification->item->id === $this->item->id;
        }
    );
});

test('does not send item resolved notification when user does not want it', function () {
    Notification::fake();

    $this->notificationService->sendItemResolvedNotification($this->item);

    // Since user has item_resolved set to false, notification should not be sent
    Notification::assertNotSentTo($this->user, ItemResolvedNotification::class);
});

test('sends item resolved notification when user wants it', function () {
    Notification::fake();

    // Update user preferences to want resolved notifications
    $this->user->updateNotificationPreferences(['item_resolved' => true]);

    $this->notificationService->sendItemResolvedNotification($this->item);

    Notification::assertSentTo(
        $this->user,
        ItemResolvedNotification::class,
        function ($notification) {
            return $notification->item->id === $this->item->id;
        }
    );
});

test('updates user notification preferences', function () {
    $newPreferences = [
        'item_verified' => false,
        'item_rejected' => false,
        'item_resolved' => true,
    ];

    $this->notificationService->updateUserNotificationPreferences($this->user, $newPreferences);

    $this->user->refresh();
    $preferences = $this->user->getNotificationPreferences();

    expect($preferences['item_verified'])->toBeFalse();
    expect($preferences['item_rejected'])->toBeFalse();
    expect($preferences['item_resolved'])->toBeTrue();
});

test('gets user notification preferences with defaults', function () {
    $userWithoutPreferences = User::factory()->create(['notification_preferences' => null]);

    $preferences = $this->notificationService->getUserNotificationPreferences($userWithoutPreferences);

    expect($preferences)->toHaveKeys(['item_verified', 'item_rejected', 'item_resolved', 'admin_updates']);
    expect($preferences['item_verified'])->toBeTrue();
    expect($preferences['item_rejected'])->toBeTrue();
    expect($preferences['item_resolved'])->toBeFalse();
    expect($preferences['admin_updates'])->toBeFalse();
});

test('user wants notification method works correctly', function () {
    expect($this->user->wantsNotification('item_verified'))->toBeTrue();
    expect($this->user->wantsNotification('item_rejected'))->toBeTrue();
    expect($this->user->wantsNotification('item_resolved'))->toBeFalse();
    expect($this->user->wantsNotification('admin_updates'))->toBeFalse();
});

test('notification contains correct item information', function () {
    $notification = new ItemVerifiedNotification($this->item);
    $mailMessage = $notification->toMail($this->user);

    expect($mailMessage->subject)->toContain('Verified');
    expect($mailMessage->greeting)->toContain($this->user->name);

    $arrayData = $notification->toArray($this->user);
    expect($arrayData['item_id'])->toBe($this->item->id);
    expect($arrayData['item_title'])->toBe($this->item->title);
    expect($arrayData['status'])->toBe('verified');
});

test('rejected notification contains admin notes when present', function () {
    $this->item->update(['admin_notes' => 'Please provide more details']);

    $notification = new ItemRejectedNotification($this->item);
    $arrayData = $notification->toArray($this->user);

    expect($arrayData['admin_notes'])->toBe('Please provide more details');
});

test('resolved notification contains resolved date', function () {
    $this->item->markAsResolved();

    $notification = new ItemResolvedNotification($this->item);
    $arrayData = $notification->toArray($this->user);

    expect($arrayData['resolved_at'])->not->toBeNull();
});
