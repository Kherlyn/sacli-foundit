<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemVerifiedNotification extends Notification implements ShouldQueue
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
        if (!$notifiable->wantsNotification('item_verified')) {
            return [];
        }

        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $itemType = ucfirst($this->item->type);
        $itemUrl = route('items.show', $this->item->id);

        return (new MailMessage)
            ->subject("Your {$itemType} Item Has Been Verified - SACLI FOUNDIT")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! Your {$itemType} item \"{$this->item->title}\" has been verified and is now publicly visible.")
            ->line("Item Details:")
            ->line("• Title: {$this->item->title}")
            ->line("• Category: {$this->item->category->name}")
            ->line("• Location: {$this->item->location}")
            ->line("• Date: {$this->item->date_occurred->format('M j, Y')}")
            ->when($this->item->admin_notes, function ($message) {
                return $message->line("Admin Notes: {$this->item->admin_notes}");
            })
            ->action('View Your Item', $itemUrl)
            ->line('Your item is now searchable by other users. We hope this helps reunite you with your belongings!')
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
            'status' => 'verified',
            'message' => "Your {$this->item->type} item \"{$this->item->title}\" has been verified.",
        ];
    }
}
