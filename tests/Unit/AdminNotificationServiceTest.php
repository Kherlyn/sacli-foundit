<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Notifications\AdminNewItemSubmissionNotification;
use App\Notifications\AdminPendingQueueAlertNotification;
use App\Notifications\AdminSystemEventNotification;
use App\Services\AdminNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminNotificationServiceTest extends TestCase
{
  use RefreshDatabase;

  private AdminNotificationService $service;
  private User $admin;
  private User $user;
  private Category $category;

  protected function setUp(): void
  {
    parent::setUp();

    $this->service = new AdminNotificationService();

    // Create test users
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->user = User::factory()->create(['role' => 'user']);

    // Create test category
    $this->category = Category::factory()->create();

    Notification::fake();
  }

  public function test_notifies_admins_about_new_item_submission()
  {
    // Create a test item
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    // Load relationships
    $item->load(['category', 'user']);

    // Call the service method
    $this->service->notifyNewItemSubmission($item);

    // Assert notification was sent to admin
    Notification::assertSentTo(
      [$this->admin],
      AdminNewItemSubmissionNotification::class,
      function ($notification) use ($item) {
        return $notification->item->id === $item->id;
      }
    );
  }

  public function test_sends_pending_queue_alert()
  {
    $pendingCount = 15;
    $thresholdHours = 24;

    $this->service->sendPendingQueueAlert($pendingCount, $thresholdHours);

    Notification::assertSentTo(
      [$this->admin],
      AdminPendingQueueAlertNotification::class,
      function ($notification) use ($pendingCount, $thresholdHours) {
        return $notification->pendingCount === $pendingCount &&
          $notification->thresholdHours === $thresholdHours;
      }
    );
  }

  public function test_notifies_system_event()
  {
    $eventType = 'test_event';
    $title = 'Test Event';
    $message = 'This is a test system event';
    $data = ['test_key' => 'test_value'];
    $priority = 'high';

    $this->service->notifySystemEvent($eventType, $title, $message, $data, $priority);

    Notification::assertSentTo(
      [$this->admin],
      AdminSystemEventNotification::class,
      function ($notification) use ($eventType, $title, $message, $data, $priority) {
        return $notification->eventType === $eventType &&
          $notification->title === $title &&
          $notification->message === $message &&
          $notification->data === $data &&
          $notification->priority === $priority;
      }
    );
  }

  public function test_checks_pending_queue_alerts()
  {
    // Create some old pending items
    Item::factory()->count(3)->create([
      'status' => 'pending',
      'created_at' => now()->subHours(25), // Older than 24 hours
    ]);

    // Create some recent pending items
    Item::factory()->count(2)->create([
      'status' => 'pending',
      'created_at' => now()->subHours(12), // Less than 24 hours
    ]);

    $this->service->checkPendingQueueAlerts();

    // Should send alert for old pending items
    Notification::assertSentTo(
      [$this->admin],
      AdminPendingQueueAlertNotification::class,
      function ($notification) {
        return $notification->pendingCount === 3 && // Only old items
          $notification->thresholdHours === 24;
      }
    );
  }

  public function test_sends_daily_statistics_summary()
  {
    $statistics = [
      'total_items' => 100,
      'pending_items' => 10,
      'verified_items' => 80,
      'rejected_items' => 5,
      'resolved_items' => 5,
      'date' => now()->format('Y-m-d'),
    ];

    $this->service->sendDailyStatisticsSummary($statistics);

    Notification::assertSentTo(
      [$this->admin],
      AdminSystemEventNotification::class,
      function ($notification) use ($statistics) {
        return $notification->eventType === 'statistics' &&
          $notification->title === 'Daily Statistics Summary' &&
          $notification->data === $statistics;
      }
    );
  }

  public function test_sends_weekly_performance_report()
  {
    $performanceData = [
      'week_ending' => now()->format('Y-m-d'),
      'total_submissions' => 50,
      'verified_items' => 40,
      'rejected_items' => 5,
      'resolved_items' => 5,
    ];

    $this->service->sendWeeklyPerformanceReport($performanceData);

    Notification::assertSentTo(
      [$this->admin],
      AdminSystemEventNotification::class,
      function ($notification) use ($performanceData) {
        return $notification->eventType === 'statistics' &&
          $notification->title === 'Weekly Performance Report' &&
          $notification->data === $performanceData;
      }
    );
  }

  public function test_notifies_category_event()
  {
    $action = 'created';
    $categoryName = 'Test Category';
    $data = ['category_id' => 1];

    $this->service->notifyCategoryEvent($action, $categoryName, $data);

    Notification::assertSentTo(
      [$this->admin],
      AdminSystemEventNotification::class,
      function ($notification) use ($action, $categoryName, $data) {
        return $notification->eventType === 'categories' &&
          $notification->title === 'New Category Created' &&
          str_contains($notification->message, $categoryName) &&
          $notification->data['action'] === $action;
      }
    );
  }

  public function test_notifies_bulk_operation()
  {
    $operation = 'verification';
    $count = 25;
    $data = ['admin_id' => $this->admin->id];

    $this->service->notifyBulkOperation($operation, $count, $data);

    Notification::assertSentTo(
      [$this->admin],
      AdminSystemEventNotification::class,
      function ($notification) use ($operation, $count, $data) {
        return $notification->eventType === 'items' &&
          $notification->title === 'Bulk Operation Completed' &&
          str_contains($notification->message, (string)$count) &&
          $notification->data['operation'] === $operation &&
          $notification->data['count'] === $count;
      }
    );
  }

  public function test_gets_unread_notification_count()
  {
    // Create some notifications for the admin
    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['test' => 'data'],
      'read_at' => null, // Unread
    ]);

    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['test' => 'data'],
      'read_at' => now(), // Read
    ]);

    $count = $this->service->getUnreadNotificationCount($this->admin);

    $this->assertEquals(1, $count);
  }

  public function test_gets_recent_notifications()
  {
    // Create multiple notifications
    for ($i = 0; $i < 15; $i++) {
      $this->admin->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => AdminSystemEventNotification::class,
        'data' => ['test' => "data_{$i}"],
        'created_at' => now()->subMinutes($i),
      ]);
    }

    $notifications = $this->service->getRecentNotifications($this->admin, 10);

    $this->assertCount(10, $notifications);
    // Should be ordered by created_at desc (most recent first)
    $this->assertEquals('data_0', $notifications->first()->data['test']);
  }

  public function test_marks_notifications_as_read()
  {
    // Create unread notifications
    $notification1 = $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['test' => 'data1'],
      'read_at' => null,
    ]);

    $notification2 = $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['test' => 'data2'],
      'read_at' => null,
    ]);

    // Mark specific notifications as read
    $this->service->markNotificationsAsRead($this->admin, [$notification1->id]);

    $this->admin->refresh();
    $this->assertNotNull($this->admin->notifications()->find($notification1->id)->read_at);
    $this->assertNull($this->admin->notifications()->find($notification2->id)->read_at);
  }

  public function test_marks_all_notifications_as_read()
  {
    // Create unread notifications
    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['test' => 'data1'],
      'read_at' => null,
    ]);

    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['test' => 'data2'],
      'read_at' => null,
    ]);

    // Mark all notifications as read
    $this->service->markNotificationsAsRead($this->admin);

    $this->admin->refresh();
    $unreadCount = $this->admin->unreadNotifications()->count();
    $this->assertEquals(0, $unreadCount);
  }

  public function test_does_not_send_notifications_when_no_admins_exist()
  {
    // Delete the admin user
    $this->admin->delete();

    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $this->service->notifyNewItemSubmission($item);

    // No notifications should be sent
    Notification::assertNothingSent();
  }

  public function test_respects_admin_notification_preferences()
  {
    // Set admin preferences to disable new submission notifications
    $this->admin->update([
      'notification_preferences' => [
        'admin_new_submissions' => false,
        'admin_queue_alerts' => true,
        'admin_system_events' => true,
      ]
    ]);

    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
    ]);

    $this->service->notifyNewItemSubmission($item);

    // No notification should be sent due to preferences
    Notification::assertNotSentTo(
      [$this->admin],
      AdminNewItemSubmissionNotification::class
    );
  }
}
