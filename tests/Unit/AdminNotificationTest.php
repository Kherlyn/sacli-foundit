<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Notifications\AdminNewItemSubmissionNotification;
use App\Notifications\AdminPendingQueueAlertNotification;
use App\Notifications\AdminSystemEventNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
  use RefreshDatabase;

  private User $admin;
  private User $user;
  private Category $category;

  protected function setUp(): void
  {
    parent::setUp();

    $this->admin = User::factory()->create([
      'role' => 'admin',
      'notification_preferences' => [
        'admin_new_submissions' => true,
        'admin_queue_alerts' => true,
        'admin_system_events' => true,
      ]
    ]);

    $this->user = User::factory()->create(['role' => 'user']);
    $this->category = Category::factory()->create();
  }

  public function test_admin_new_item_submission_notification_mail_content()
  {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'title' => 'Lost iPhone',
      'type' => 'lost',
      'location' => 'Central Park',
    ]);

    $item->load(['category', 'user']);

    $notification = new AdminNewItemSubmissionNotification($item);
    $mailMessage = $notification->toMail($this->admin);

    $this->assertEquals('New Lost Item Submission - SACLI FOUNDIT Admin', $mailMessage->subject);
    $this->assertStringContainsString('Lost iPhone', $mailMessage->render());
    $this->assertStringContainsString('Central Park', $mailMessage->render());
    $this->assertStringContainsString($this->user->name, $mailMessage->render());
  }

  public function test_admin_new_item_submission_notification_database_content()
  {
    $item = Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $this->category->id,
      'title' => 'Found Wallet',
      'type' => 'found',
    ]);

    $item->load(['category', 'user']);

    $notification = new AdminNewItemSubmissionNotification($item);
    $databaseData = $notification->toDatabase($this->admin);

    $this->assertEquals('new_submission', $databaseData['type']);
    $this->assertEquals($item->id, $databaseData['item_id']);
    $this->assertEquals('Found Wallet', $databaseData['item_title']);
    $this->assertEquals('found', $databaseData['item_type']);
    $this->assertEquals($this->user->name, $databaseData['user_name']);
    $this->assertStringContainsString('Found Wallet', $databaseData['message']);
  }

  public function test_admin_new_item_submission_notification_respects_preferences()
  {
    $this->admin->update([
      'notification_preferences' => [
        'admin_new_submissions' => false,
      ]
    ]);

    $item = Item::factory()->create();
    $notification = new AdminNewItemSubmissionNotification($item);
    $channels = $notification->via($this->admin);

    $this->assertEmpty($channels);
  }

  public function test_admin_pending_queue_alert_notification_mail_content()
  {
    $pendingCount = 15;
    $thresholdHours = 24;

    $notification = new AdminPendingQueueAlertNotification($pendingCount, $thresholdHours);
    $mailMessage = $notification->toMail($this->admin);

    $this->assertEquals('Pending Items Queue Alert - SACLI FOUNDIT Admin', $mailMessage->subject);
    $this->assertStringContainsString('15 items', $mailMessage->render());
    $this->assertStringContainsString('24 hours', $mailMessage->render());
  }

  public function test_admin_pending_queue_alert_notification_high_volume()
  {
    $pendingCount = 25; // High volume
    $thresholdHours = 24;

    $notification = new AdminPendingQueueAlertNotification($pendingCount, $thresholdHours);
    $mailMessage = $notification->toMail($this->admin);

    $this->assertStringContainsString('High volume alert', $mailMessage->render());
  }

  public function test_admin_pending_queue_alert_notification_database_content()
  {
    $pendingCount = 8;
    $thresholdHours = 48;

    $notification = new AdminPendingQueueAlertNotification($pendingCount, $thresholdHours);
    $databaseData = $notification->toDatabase($this->admin);

    $this->assertEquals('queue_alert', $databaseData['type']);
    $this->assertEquals(8, $databaseData['pending_count']);
    $this->assertEquals(48, $databaseData['threshold_hours']);
    $this->assertEquals('normal', $databaseData['priority']);
    $this->assertStringContainsString('8 items pending', $databaseData['message']);
  }

  public function test_admin_system_event_notification_mail_content()
  {
    $eventType = 'statistics';
    $title = 'Daily Statistics Summary';
    $message = 'Daily system statistics are available.';
    $data = ['total_items' => 100, 'pending_items' => 10];
    $priority = 'normal';

    $notification = new AdminSystemEventNotification($eventType, $title, $message, $data, $priority);
    $mailMessage = $notification->toMail($this->admin);

    $this->assertEquals('System Event Alert - SACLI FOUNDIT Admin', $mailMessage->subject);
    $this->assertStringContainsString($title, $mailMessage->render());
    $this->assertStringContainsString($message, $mailMessage->render());
    $this->assertStringContainsString('Total Items: 100', $mailMessage->render());
    $this->assertStringContainsString('Pending Items: 10', $mailMessage->render());
  }

  public function test_admin_system_event_notification_database_content()
  {
    $eventType = 'categories';
    $title = 'New Category Created';
    $message = 'Category "Electronics" has been created.';
    $data = ['category_name' => 'Electronics', 'action' => 'created'];
    $priority = 'high';

    $notification = new AdminSystemEventNotification($eventType, $title, $message, $data, $priority);
    $databaseData = $notification->toDatabase($this->admin);

    $this->assertEquals('system_event', $databaseData['type']);
    $this->assertEquals($eventType, $databaseData['event_type']);
    $this->assertEquals($title, $databaseData['title']);
    $this->assertEquals($message, $databaseData['message']);
    $this->assertEquals($data, $databaseData['data']);
    $this->assertEquals($priority, $databaseData['priority']);
  }

  public function test_admin_system_event_notification_channels_based_on_priority()
  {
    // Normal priority - only database
    $normalNotification = new AdminSystemEventNotification(
      'test',
      'Test',
      'Test message',
      [],
      'normal'
    );
    $normalChannels = $normalNotification->via($this->admin);
    $this->assertEquals(['database'], $normalChannels);

    // High priority - database and mail
    $highNotification = new AdminSystemEventNotification(
      'test',
      'Test',
      'Test message',
      [],
      'high'
    );
    $highChannels = $highNotification->via($this->admin);
    $this->assertContains('database', $highChannels);
    $this->assertContains('mail', $highChannels);
  }

  public function test_admin_system_event_notification_respects_preferences()
  {
    $this->admin->update([
      'notification_preferences' => [
        'admin_system_events' => false,
      ]
    ]);

    $notification = new AdminSystemEventNotification(
      'test',
      'Test',
      'Test message',
      [],
      'high'
    );
    $channels = $notification->via($this->admin);

    $this->assertEmpty($channels);
  }

  public function test_admin_system_event_notification_action_url_generation()
  {
    $statisticsNotification = new AdminSystemEventNotification(
      'statistics',
      'Stats',
      'Message',
      [],
      'normal'
    );
    $databaseData = $statisticsNotification->toDatabase($this->admin);
    $this->assertStringContainsString('statistics', $databaseData['action_url']);

    $categoriesNotification = new AdminSystemEventNotification(
      'categories',
      'Categories',
      'Message',
      [],
      'normal'
    );
    $databaseData = $categoriesNotification->toDatabase($this->admin);
    $this->assertStringContainsString('categories', $databaseData['action_url']);

    $itemsNotification = new AdminSystemEventNotification(
      'items',
      'Items',
      'Message',
      [],
      'normal'
    );
    $databaseData = $itemsNotification->toDatabase($this->admin);
    $this->assertStringContainsString('items', $databaseData['action_url']);

    $defaultNotification = new AdminSystemEventNotification(
      'unknown',
      'Unknown',
      'Message',
      [],
      'normal'
    );
    $databaseData = $defaultNotification->toDatabase($this->admin);
    $this->assertStringContainsString('dashboard', $databaseData['action_url']);
  }
}
