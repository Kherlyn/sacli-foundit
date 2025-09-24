<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Notifications\AdminSystemEventNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminNotificationEndpointsTest extends TestCase
{
  use RefreshDatabase;

  private User $admin;
  private User $user;

  protected function setUp(): void
  {
    parent::setUp();

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->user = User::factory()->create(['role' => 'user']);
  }

  public function test_admin_can_get_notifications()
  {
    // Create some notifications for the admin
    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => [
        'type' => 'system_event',
        'title' => 'Test Notification',
        'message' => 'This is a test notification',
      ],
      'read_at' => null,
    ]);

    $response = $this->actingAs($this->admin)
      ->getJson(route('admin.notifications'));

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'notifications',
        'unread_count',
      ])
      ->assertJson([
        'success' => true,
        'unread_count' => 1,
      ]);
  }

  public function test_admin_can_get_unread_notifications_only()
  {
    // Create read and unread notifications
    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['title' => 'Read Notification'],
      'read_at' => now(),
    ]);

    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['title' => 'Unread Notification'],
      'read_at' => null,
    ]);

    $response = $this->actingAs($this->admin)
      ->getJson(route('admin.notifications', ['unread_only' => true]));

    $response->assertStatus(200)
      ->assertJsonCount(1, 'notifications');
  }

  public function test_admin_can_mark_specific_notifications_as_read()
  {
    $notification = $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['title' => 'Test Notification'],
      'read_at' => null,
    ]);

    $response = $this->actingAs($this->admin)
      ->postJson(route('admin.notifications.mark-read'), [
        'notification_ids' => [$notification->id],
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'unread_count' => 0,
      ]);

    $this->assertNotNull($notification->fresh()->read_at);
  }

  public function test_admin_can_mark_all_notifications_as_read()
  {
    // Create multiple unread notifications
    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['title' => 'Notification 1'],
      'read_at' => null,
    ]);

    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['title' => 'Notification 2'],
      'read_at' => null,
    ]);

    $response = $this->actingAs($this->admin)
      ->postJson(route('admin.notifications.mark-read'), [
        'mark_all' => true,
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'unread_count' => 0,
      ]);

    $this->assertEquals(0, $this->admin->unreadNotifications()->count());
  }

  public function test_admin_can_send_test_notifications()
  {
    Notification::fake();

    $response = $this->actingAs($this->admin)
      ->postJson(route('admin.notifications.test'), [
        'type' => 'system_event',
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
      ]);
  }

  public function test_admin_can_send_test_new_submission_notification()
  {
    Notification::fake();

    // Create a test item first
    $category = Category::factory()->create();
    Item::factory()->create([
      'user_id' => $this->user->id,
      'category_id' => $category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->postJson(route('admin.notifications.test'), [
        'type' => 'new_submission',
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
      ]);
  }

  public function test_admin_can_send_test_queue_alert_notification()
  {
    Notification::fake();

    // Create some pending items
    $category = Category::factory()->create();
    Item::factory()->count(3)->create([
      'status' => 'pending',
      'category_id' => $category->id,
    ]);

    $response = $this->actingAs($this->admin)
      ->postJson(route('admin.notifications.test'), [
        'type' => 'queue_alert',
      ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
      ]);
  }

  public function test_regular_user_cannot_access_admin_notification_endpoints()
  {
    $response = $this->actingAs($this->user)
      ->getJson(route('admin.notifications'));

    $response->assertStatus(403);

    $response = $this->actingAs($this->user)
      ->postJson(route('admin.notifications.mark-read'));

    $response->assertStatus(403);

    $response = $this->actingAs($this->user)
      ->postJson(route('admin.notifications.test'), ['type' => 'system_event']);

    $response->assertStatus(403);
  }

  public function test_unauthenticated_user_cannot_access_admin_notification_endpoints()
  {
    $response = $this->getJson(route('admin.notifications'));
    $response->assertStatus(401);

    $response = $this->postJson(route('admin.notifications.mark-read'));
    $response->assertStatus(401);

    $response = $this->postJson(route('admin.notifications.test'), ['type' => 'system_event']);
    $response->assertStatus(401);
  }

  public function test_admin_notification_endpoints_validate_input()
  {
    // Test invalid notification type for test endpoint
    $response = $this->actingAs($this->admin)
      ->postJson(route('admin.notifications.test'), [
        'type' => 'invalid_type',
      ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['type']);

    // Test invalid notification IDs for mark as read
    $response = $this->actingAs($this->admin)
      ->postJson(route('admin.notifications.mark-read'), [
        'notification_ids' => ['invalid-id'],
      ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['notification_ids.0']);
  }

  public function test_admin_notifications_endpoint_respects_limit_parameter()
  {
    // Create more notifications than the default limit
    for ($i = 0; $i < 15; $i++) {
      $this->admin->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => AdminSystemEventNotification::class,
        'data' => ['title' => "Notification {$i}"],
        'created_at' => now()->subMinutes($i),
      ]);
    }

    $response = $this->actingAs($this->admin)
      ->getJson(route('admin.notifications', ['limit' => 5]));

    $response->assertStatus(200)
      ->assertJsonCount(5, 'notifications');
  }

  public function test_admin_dashboard_includes_notification_data()
  {
    // Create some notifications
    $this->admin->notifications()->create([
      'id' => \Illuminate\Support\Str::uuid(),
      'type' => AdminSystemEventNotification::class,
      'data' => ['title' => 'Test Notification'],
      'read_at' => null,
    ]);

    $response = $this->actingAs($this->admin)
      ->get(route('admin.dashboard'));

    $response->assertStatus(200);

    // Check that notification data is passed to the view
    $response->assertViewHas('unreadNotificationCount', 1);
    $response->assertViewHas('recentNotifications');
  }
}
