<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\Item;
use App\Models\User;
use App\Notifications\AdminNewChatMessageNotification;
use App\Notifications\AdminNewItemSubmissionNotification;
use App\Notifications\AdminPendingQueueAlertNotification;
use App\Notifications\AdminSystemEventNotification;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
  /**
   * Send notification to all admins about a new item submission.
   */
  public function notifyNewItemSubmission(Item $item): void
  {
    $admins = User::admins()->get();

    if ($admins->isNotEmpty()) {
      Notification::send($admins, new AdminNewItemSubmissionNotification($item));
    }
  }

  /**
   * Send notification to all admins about a new chat message.
   */
  public function notifyNewChatMessage(ChatMessage $message): void
  {
    $admins = Admin::all();

    if ($admins->isNotEmpty()) {
      Notification::send($admins, new AdminNewChatMessageNotification($message));
    }
  }

  /**
   * Send pending queue alert to admins.
   */
  public function sendPendingQueueAlert(int $pendingCount, int $thresholdHours = 24): void
  {
    $admins = User::admins()->get();

    if ($admins->isNotEmpty()) {
      Notification::send($admins, new AdminPendingQueueAlertNotification($pendingCount, $thresholdHours));
    }
  }

  /**
   * Send system event notification to admins.
   */
  public function notifySystemEvent(
    string $eventType,
    string $title,
    string $message,
    array $data = [],
    string $priority = 'normal'
  ): void {
    $admins = User::admins()->get();

    if ($admins->isNotEmpty()) {
      Notification::send(
        $admins,
        new AdminSystemEventNotification($eventType, $title, $message, $data, $priority)
      );
    }
  }

  /**
   * Check and send pending queue alerts if needed.
   */
  public function checkPendingQueueAlerts(): void
  {
    $pendingCount = Item::pending()->count();

    // Send alert if there are items pending for more than 24 hours
    $oldPendingCount = Item::pending()
      ->where('created_at', '<', now()->subHours(24))
      ->count();

    if ($oldPendingCount > 0) {
      $this->sendPendingQueueAlert($oldPendingCount, 24);
    }

    // Send high volume alert if queue exceeds threshold
    if ($pendingCount > 10) {
      $this->notifySystemEvent(
        'queue_volume',
        'High Volume Alert',
        "The pending verification queue has {$pendingCount} items, exceeding the recommended threshold.",
        ['pending_count' => $pendingCount, 'threshold' => 10],
        'high'
      );
    }
  }

  /**
   * Send daily statistics summary to admins.
   */
  public function sendDailyStatisticsSummary(array $statistics): void
  {
    $this->notifySystemEvent(
      'statistics',
      'Daily Statistics Summary',
      'Daily system statistics and performance metrics are available.',
      $statistics,
      'normal'
    );
  }

  /**
   * Send weekly performance report to admins.
   */
  public function sendWeeklyPerformanceReport(array $performanceData): void
  {
    $this->notifySystemEvent(
      'statistics',
      'Weekly Performance Report',
      'Weekly system performance and usage report is ready for review.',
      $performanceData,
      'normal'
    );
  }

  /**
   * Notify admins about category management events.
   */
  public function notifyCategoryEvent(string $action, string $categoryName, array $data = []): void
  {
    $title = match ($action) {
      'created' => 'New Category Created',
      'updated' => 'Category Updated',
      'deleted' => 'Category Deleted',
      default => 'Category Event',
    };

    $message = "Category \"{$categoryName}\" has been {$action}.";

    $this->notifySystemEvent(
      'categories',
      $title,
      $message,
      array_merge(['category_name' => $categoryName, 'action' => $action], $data),
      'normal'
    );
  }

  /**
   * Notify admins about bulk operations.
   */
  public function notifyBulkOperation(string $operation, int $count, array $data = []): void
  {
    $title = "Bulk Operation Completed";
    $message = "Bulk {$operation} operation completed on {$count} items.";

    $this->notifySystemEvent(
      'items',
      $title,
      $message,
      array_merge(['operation' => $operation, 'count' => $count], $data),
      $count > 50 ? 'high' : 'normal'
    );
  }

  /**
   * Get unread notification count for admin dashboard.
   */
  public function getUnreadNotificationCount(User $admin): int
  {
    return $admin->unreadNotifications()->count();
  }

  /**
   * Get recent notifications for admin dashboard.
   */
  public function getRecentNotifications(User $admin, int $limit = 10): \Illuminate\Notifications\DatabaseNotificationCollection
  {
    return $admin->notifications()
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get();
  }

  /**
   * Mark notifications as read for admin.
   */
  public function markNotificationsAsRead(User $admin, array $notificationIds = []): void
  {
    if (empty($notificationIds)) {
      $admin->unreadNotifications->markAsRead();
    } else {
      $admin->unreadNotifications()
        ->whereIn('id', $notificationIds)
        ->update(['read_at' => now()]);
    }
  }
}
