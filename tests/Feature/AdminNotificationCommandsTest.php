<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Services\AdminNotificationService;
use App\Services\StatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminNotificationCommandsTest extends TestCase
{
  use RefreshDatabase;

  private User $admin;
  private Category $category;

  protected function setUp(): void
  {
    parent::setUp();

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->category = Category::factory()->create();

    Notification::fake();
  }

  public function test_check_pending_queue_alerts_command_runs_successfully()
  {
    // Create some old pending items
    Item::factory()->count(3)->create([
      'status' => 'pending',
      'category_id' => $this->category->id,
      'created_at' => now()->subHours(25),
    ]);

    // Create some recent items
    Item::factory()->count(2)->create([
      'status' => 'verified',
      'category_id' => $this->category->id,
    ]);

    $this->artisan('admin:check-pending-alerts')
      ->expectsOutput('Checking pending queue alerts with 24 hour threshold...')
      ->expectsOutput('Queue check completed:')
      ->expectsOutput('Pending queue alerts check completed successfully.')
      ->assertExitCode(0);
  }

  public function test_check_pending_queue_alerts_command_with_custom_threshold()
  {
    Item::factory()->count(2)->create([
      'status' => 'pending',
      'category_id' => $this->category->id,
      'created_at' => now()->subHours(49), // Older than 48 hours
    ]);

    $this->artisan('admin:check-pending-alerts', ['--threshold' => 48])
      ->expectsOutput('Checking pending queue alerts with 48 hour threshold...')
      ->assertExitCode(0);
  }

  public function test_send_weekly_performance_report_command_runs_successfully()
  {
    // Create some test data
    Item::factory()->count(5)->create([
      'status' => 'verified',
      'category_id' => $this->category->id,
      'created_at' => now()->subDays(3),
    ]);

    Item::factory()->count(2)->create([
      'status' => 'rejected',
      'category_id' => $this->category->id,
      'created_at' => now()->subDays(2),
    ]);

    $this->artisan('admin:send-weekly-report')
      ->expectsOutput('Generating weekly performance report...')
      ->expectsOutput('Weekly performance report sent successfully.')
      ->expectsOutput('Report summary:')
      ->assertExitCode(0);
  }

  public function test_check_pending_queue_alerts_command_handles_errors_gracefully()
  {
    // Mock the AdminNotificationService to throw an exception
    $this->mock(AdminNotificationService::class, function ($mock) {
      $mock->shouldReceive('checkPendingQueueAlerts')
        ->andThrow(new \Exception('Test error'));
    });

    $this->artisan('admin:check-pending-alerts')
      ->expectsOutput('Error checking pending queue alerts: Test error')
      ->assertExitCode(1);
  }

  public function test_send_weekly_performance_report_command_handles_errors_gracefully()
  {
    // Mock the StatisticsService to throw an exception
    $this->mock(StatisticsService::class, function ($mock) {
      $mock->shouldReceive('getSubmissionCount')
        ->andThrow(new \Exception('Statistics error'));
    });

    $this->artisan('admin:send-weekly-report')
      ->expectsOutput('Error sending weekly performance report: Statistics error')
      ->assertExitCode(1);
  }

  public function test_check_pending_queue_alerts_command_sends_daily_statistics_at_correct_time()
  {
    // Mock the current time to be 9:02 AM
    $this->travelTo(now()->setTime(9, 2));

    Item::factory()->count(10)->create([
      'status' => 'verified',
      'category_id' => $this->category->id,
    ]);

    Item::factory()->count(3)->create([
      'status' => 'pending',
      'category_id' => $this->category->id,
    ]);

    $this->artisan('admin:check-pending-alerts')
      ->expectsOutput('Daily statistics summary sent to admins.')
      ->assertExitCode(0);
  }

  public function test_check_pending_queue_alerts_command_does_not_send_daily_statistics_at_wrong_time()
  {
    // Mock the current time to be 10:00 AM (not 9 AM)
    $this->travelTo(now()->setTime(10, 0));

    Item::factory()->count(5)->create([
      'status' => 'verified',
      'category_id' => $this->category->id,
    ]);

    $this->artisan('admin:check-pending-alerts')
      ->doesntExpectOutput('Daily statistics summary sent to admins.')
      ->assertExitCode(0);
  }

  public function test_commands_work_with_no_admin_users()
  {
    // Delete the admin user
    $this->admin->delete();

    $this->artisan('admin:check-pending-alerts')
      ->assertExitCode(0);

    $this->artisan('admin:send-weekly-report')
      ->assertExitCode(0);
  }

  public function test_commands_work_with_no_items()
  {
    // No items in database
    $this->artisan('admin:check-pending-alerts')
      ->expectsOutput('- Total pending items: 0')
      ->assertExitCode(0);

    $this->artisan('admin:send-weekly-report')
      ->expectsOutput('- Total submissions: 0')
      ->assertExitCode(0);
  }
}
