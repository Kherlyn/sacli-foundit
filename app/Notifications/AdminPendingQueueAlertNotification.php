<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPendingQueueAlertNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(
    public int $pendingCount,
    public int $thresholdHours = 24
  ) {
    //
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    // Check admin preferences for queue alerts
    if (!$notifiable->wantsNotification('admin_queue_alerts')) {
      return [];
    }

    return ['mail', 'database'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable): MailMessage
  {
    $adminUrl = route('admin.pending-items');
    $dashboardUrl = route('admin.dashboard');

    $message = (new MailMessage)
      ->subject("Pending Items Queue Alert - SACLI FOUNDIT Admin")
      ->greeting("Hello {$notifiable->name}!")
      ->line("There are currently {$this->pendingCount} items in the pending verification queue.")
      ->line("Some items have been waiting for more than {$this->thresholdHours} hours for review.");

    if ($this->pendingCount > 10) {
      $message->line("⚠️ High volume alert: The queue has exceeded the recommended threshold.");
    }

    return $message
      ->action('Review Pending Items', $adminUrl)
      ->line('Please review these submissions to maintain good user experience.')
      ->action('View Dashboard', $dashboardUrl)
      ->salutation('SACLI FOUNDIT Admin System');
  }

  /**
   * Get the database representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toDatabase(object $notifiable): array
  {
    return [
      'type' => 'queue_alert',
      'pending_count' => $this->pendingCount,
      'threshold_hours' => $this->thresholdHours,
      'priority' => $this->pendingCount > 10 ? 'high' : 'normal',
      'message' => "{$this->pendingCount} items pending verification for over {$this->thresholdHours} hours",
      'action_url' => route('admin.pending-items'),
    ];
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    return $this->toDatabase($notifiable);
  }
}
