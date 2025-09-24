<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewItemSubmissionNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(
    public Item $item
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
    // Check admin preferences for new submissions
    if (!$notifiable->wantsNotification('admin_new_submissions')) {
      return [];
    }

    return ['mail', 'database'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable): MailMessage
  {
    $itemType = ucfirst($this->item->type);
    $adminUrl = route('admin.pending-items');
    $itemDetailUrl = route('admin.items.show', $this->item->id);

    return (new MailMessage)
      ->subject("New {$itemType} Item Submission - SACLI FOUNDIT Admin")
      ->greeting("Hello {$notifiable->name}!")
      ->line("A new {$itemType} item has been submitted and requires verification.")
      ->line("Submission Details:")
      ->line("• ID: #{$this->item->id}")
      ->line("• Title: {$this->item->title}")
      ->line("• Category: {$this->item->category->name}")
      ->line("• Submitted by: {$this->item->user->name} ({$this->item->user->email})")
      ->line("• Location: {$this->item->location}")
      ->line("• Date: {$this->item->date_occurred->format('M j, Y')}")
      ->line("• Submitted: {$this->item->created_at->format('M j, Y g:i A')}")
      ->action('Review Item', $itemDetailUrl)
      ->line('Please review and verify this submission as soon as possible.')
      ->action('View All Pending Items', $adminUrl)
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
      'type' => 'new_submission',
      'item_id' => $this->item->id,
      'item_title' => $this->item->title,
      'item_type' => $this->item->type,
      'user_name' => $this->item->user->name,
      'user_email' => $this->item->user->email,
      'category' => $this->item->category->name,
      'location' => $this->item->location,
      'submitted_at' => $this->item->created_at,
      'message' => "New {$this->item->type} item \"{$this->item->title}\" submitted by {$this->item->user->name}",
      'action_url' => route('admin.items.show', $this->item->id),
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
