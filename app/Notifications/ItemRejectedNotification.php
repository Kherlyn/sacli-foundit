<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemRejectedNotification extends Notification implements ShouldQueue
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
        // Check user preferences
        if (!$notifiable->wantsNotification('item_rejected')) {
            return [];
        }

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $itemType = ucfirst($this->item->type);
        $submitUrl = route('items.create');

        return (new MailMessage)
            ->subject("Your {$itemType} Item Submission Needs Attention - SACLI FOUNDIT")
            ->greeting("Hello {$notifiable->name}!")
            ->line("We've reviewed your {$itemType} item submission \"{$this->item->title}\" and it requires some attention before it can be published.")
            ->line("Item Details:")
            ->line("• Title: {$this->item->title}")
            ->line("• Category: {$this->item->category->name}")
            ->line("• Location: {$this->item->location}")
            ->line("• Date: {$this->item->date_occurred->format('M j, Y')}")
            ->when($this->item->admin_notes, function ($message) {
                return $message->line("Reason: {$this->item->admin_notes}");
            })
            ->line('Please review the feedback above and feel free to submit a new item with the necessary corrections.')
            ->action('Submit New Item', $submitUrl)
            ->line('If you have any questions, please don\'t hesitate to contact our support team.')
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
            'status' => 'rejected',
            'message' => "Your {$this->item->type} item \"{$this->item->title}\" needs attention before publication.",
            'admin_notes' => $this->item->admin_notes,
        ];
    }
}
