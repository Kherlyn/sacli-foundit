<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminSystemEventNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(
    public string $eventType,
    public string $title,
    public string $message,
    public array $data = [],
    public string $priority = 'normal'
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
    // Check admin preferences for system events
    if (!$notifiable->wantsNotification('admin_system_events')) {
      return [];
    }

    $channels = ['database'];

    // Send email for high priority events
    if ($this->priority === 'high') {
      $channels[] = 'mail';
    }

    return $channels;
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable): MailMessage
  {
    $dashboardUrl = route('admin.dashboard');
    $statisticsUrl = route('admin.statistics');

    $mailMessage = (new MailMessage)
      ->subject("System Event Alert - SACLI FOUNDIT Admin")
      ->greeting("Hello {$notifiable->name}!")
      ->line($this->title)
      ->line($this->message);

    // Add additional data if available
    if (!empty($this->data)) {
      $mailMessage->line("Event Details:");
      foreach ($this->data as $key => $value) {
        $label = ucwords(str_replace('_', ' ', $key));
        $mailMessage->line("• {$label}: {$value}");
      }
    }

    return $mailMessage
      ->action('View Dashboard', $dashboardUrl)
      ->when($this->eventType === 'statistics', function ($message) use ($statisticsUrl) {
        return $message->action('View Statistics', $statisticsUrl);
      })
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
      'type' => 'system_event',
      'event_type' => $this->eventType,
      'title' => $this->title,
      'message' => $this->message,
      'data' => $this->data,
      'priority' => $this->priority,
      'action_url' => $this->getActionUrl(),
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

  /**
   * Get the appropriate action URL based on event type.
   */
  private function getActionUrl(): string
  {
    return match ($this->eventType) {
      'statistics' => route('admin.statistics'),
      'categories' => route('admin.categories'),
      'items' => route('admin.items'),
      default => route('admin.dashboard'),
    };
  }
}
