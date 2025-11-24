<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use App\Repositories\ChatRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ChatService
{
  /**
   * Create a new ChatService instance.
   */
  public function __construct(
    private ChatRepository $chatRepository,
    private AdminNotificationService $notificationService
  ) {}

  /**
   * Get or create a chat session for a user.
   *
   * @param User $user
   * @return ChatSession
   */
  public function getOrCreateUserSession(User $user): ChatSession
  {
    return $this->chatRepository->findOrCreateSessionForUser($user->id);
  }

  /**
   * Send a message from a user.
   *
   * @param User $user
   * @param string $message
   * @return ChatMessage
   * @throws ValidationException
   */
  public function sendUserMessage(User $user, string $message): ChatMessage
  {
    // Validate message is not empty
    $trimmedMessage = trim($message);
    if (empty($trimmedMessage)) {
      throw ValidationException::withMessages([
        'message' => ['Message cannot be empty']
      ]);
    }

    // Get or create session for user
    $session = $this->getOrCreateUserSession($user);

    // Create the message
    $chatMessage = $this->chatRepository->createMessage([
      'chat_session_id' => $session->id,
      'sender_type' => 'user',
      'sender_id' => $user->id,
      'message' => $trimmedMessage,
    ]);

    // Notify admins of new message
    $this->notifyAdminsOfNewMessage($chatMessage);

    Log::info('User message sent', [
      'user_id' => $user->id,
      'session_id' => $session->id,
      'message_id' => $chatMessage->id,
    ]);

    return $chatMessage;
  }

  /**
   * Send a message from an admin.
   *
   * @param Admin $admin
   * @param ChatSession $session
   * @param string $message
   * @return ChatMessage
   * @throws ValidationException
   */
  public function sendAdminMessage(Admin $admin, ChatSession $session, string $message): ChatMessage
  {
    // Validate message is not empty
    $trimmedMessage = trim($message);
    if (empty($trimmedMessage)) {
      throw ValidationException::withMessages([
        'message' => ['Message cannot be empty']
      ]);
    }

    // Create the message
    $chatMessage = $this->chatRepository->createMessage([
      'chat_session_id' => $session->id,
      'sender_type' => 'admin',
      'sender_id' => $admin->id,
      'message' => $trimmedMessage,
    ]);

    Log::info('Admin message sent', [
      'admin_id' => $admin->id,
      'session_id' => $session->id,
      'message_id' => $chatMessage->id,
    ]);

    return $chatMessage;
  }

  /**
   * Get messages for a session with pagination.
   *
   * @param ChatSession $session
   * @param int $limit
   * @return Collection
   */
  public function getSessionMessages(ChatSession $session, int $limit = 50): Collection
  {
    $sessionWithMessages = $this->chatRepository->getSessionWithMessages($session->id, $limit);
    return $sessionWithMessages->messages;
  }

  /**
   * Get unread message count for a user.
   *
   * @param User $user
   * @return int
   */
  public function getUnreadCountForUser(User $user): int
  {
    $session = $user->chatSession;

    if (!$session) {
      return 0;
    }

    return $this->chatRepository->getUnreadMessagesCount($session->id, 'user');
  }

  /**
   * Get all chat sessions for admin dashboard.
   *
   * @return Collection
   */
  public function getAllSessionsForAdmin(): Collection
  {
    return $this->chatRepository->getAllSessionsOrderedByActivity();
  }

  /**
   * Mark messages in a session as read by the specified reader type.
   *
   * @param ChatSession $session
   * @param string $readerType Either 'user' or 'admin'
   * @return void
   */
  public function markSessionAsReadBy(ChatSession $session, string $readerType): void
  {
    $this->chatRepository->markMessagesAsRead($session->id, $readerType);

    Log::info('Messages marked as read', [
      'session_id' => $session->id,
      'reader_type' => $readerType,
    ]);
  }

  /**
   * Notify admins of a new user message.
   *
   * @param ChatMessage $message
   * @return void
   */
  private function notifyAdminsOfNewMessage(ChatMessage $message): void
  {
    try {
      $this->notificationService->notifyNewChatMessage($message);

      Log::info('Admins notified of new chat message', [
        'message_id' => $message->id,
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to notify admins of new chat message', [
        'message_id' => $message->id,
        'error' => $e->getMessage(),
      ]);
    }
  }
}
