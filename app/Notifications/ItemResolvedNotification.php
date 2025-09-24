<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemResolvedNotification extends Notification implements ShouldQueue
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
        if (!$notifiable->wantsNotification('item_resolved')) {
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
        $dashboardUrl = route('dashboard');

        return (new MailMessage)
            ->subject("Your {$itemType} Item Has Been Resolved - SACLI FOUNDIT")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Wonderful news! Your {$itemType} item \"{$this->item->title}\" has been marked as resolved.")
            ->line("Item Details:")
            ->line("• Title: {$this->item->title}")
            ->line("• Category: {$this->item->category->name}")
            ->line("• Location: {$this->item->location}")
            ->line("• Date: {$this->item->date_occurred->format('M j, Y')}")
            ->line("• Resolved: {$this->item->resolved_at->format('M j, Y')}")
            ->line('We\'re thrilled that your item has been successfully reunited! This is what SACLI FOUNDIT is all about - bringing lost items back to their rightful owners.')
            ->action('View Dashboard', $dashboardUrl)
            ->line('Thank you for using SACLI FOUNDIT and helping make our community a better place!')
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
            'status' => 'resolved',
            'message' => "Your {$this->item->type} item \"{$this->item->title}\" has been resolved!",
            'resolved_at' => $this->item->resolved_at,
        ];
    }
}
