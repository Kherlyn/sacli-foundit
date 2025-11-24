<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ChatMessage $message
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
        // Check admin preferences for chat messages
        if (!$notifiable->wantsNotification('admin_chat_messages')) {
            return [];
        }

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $session = $this->message->chatSession;
        $user = $session->user;
        $chatUrl = route('admin.chat.show', $session->id);
        $messagePreview = strlen($this->message->message) > 100
            ? substr($this->message->message, 0, 100) . '...'
            : $this->message->message;

        return (new MailMessage)
            ->subject("New Chat Message - SACLI FOUNDIT Admin")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A user has sent a new chat message requiring attention.")
            ->line("User Details:")
            ->line("• Name: {$user->name}")
            ->line("• Email: {$user->email}")
            ->line("• Message Preview: \"{$messagePreview}\"")
            ->line("• Sent: {$this->message->created_at->format('M j, Y g:i A')}")
            ->action('View Chat Session', $chatUrl)
            ->line('Please respond to the user as soon as possible.')
            ->salutation('SACLI FOUNDIT Admin System');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $session = $this->message->chatSession;
        $user = $session->user;
        $messagePreview = strlen($this->message->message) > 100
            ? substr($this->message->message, 0, 100) . '...'
            : $this->message->message;

        return [
            'type' => 'new_chat_message',
            'message_id' => $this->message->id,
            'session_id' => $session->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'message_preview' => $messagePreview,
            'sent_at' => $this->message->created_at,
            'message' => "New chat message from {$user->name}: \"{$messagePreview}\"",
            'action_url' => route('admin.chat.show', $session->id),
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
