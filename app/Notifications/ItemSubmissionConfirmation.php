<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemSubmissionConfirmation extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $itemType = ucfirst($this->item->type);
        $dashboardUrl = route('dashboard');

        return (new MailMessage)
            ->subject("Your {$itemType} Item Submission Received - SACLI FOUNDIT")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Thank you for submitting your {$itemType} item to SACLI FOUNDIT. We've received your submission and it's now under review.")
            ->line("Submission Details:")
            ->line("• Reference ID: #{$this->item->id}")
            ->line("• Title: {$this->item->title}")
            ->line("• Category: {$this->item->category->name}")
            ->line("• Location: {$this->item->location}")
            ->line("• Date: {$this->item->date_occurred->format('M j, Y')}")
            ->line('Our team will review your submission within 24-48 hours. Once approved, your item will be publicly searchable and you\'ll receive a confirmation email.')
            ->action('View My Items', $dashboardUrl)
            ->line('We appreciate your contribution to helping reunite lost items with their owners!')
            ->line('Thank you for using SACLI FOUNDIT!')
            ->salutation('Best regards, The SACLI FOUNDIT Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_title' => $this->item->title,
            'item_type' => $this->item->type,
            'status' => 'submitted',
            'message' => "Your {$this->item->type} item \"{$this->item->title}\" has been submitted for review.",
        ];
    }
}
