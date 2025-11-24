<?php

namespace App\Repositories;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ChatRepository
{
  /**
   * Find or create a chat session for a user.
   *
   * @param int $userId
   * @return ChatSession
   */
  public function findOrCreateSessionForUser(int $userId): ChatSession
  {
    return ChatSession::firstOrCreate(
      ['user_id' => $userId],
      [
        'status' => 'open',
        'last_message_at' => null,
      ]
    );
  }

  /**
   * Get a chat session with its messages.
   * Eager loads user and messages to prevent N+1 queries.
   *
   * @param int $sessionId
   * @param int $limit
   * @return ChatSession
   */
  public function getSessionWithMessages(int $sessionId, int $limit = 50): ChatSession
  {
    return ChatSession::with([
      'user',
      'messages' => function ($query) use ($limit) {
        $query->orderBy('created_at', 'asc')
          ->limit($limit);
      }
    ])->findOrFail($sessionId);
  }

  /**
   * Create a new chat message.
   *
   * @param array $data
   * @return ChatMessage
   */
  public function createMessage(array $data): ChatMessage
  {
    $message = ChatMessage::create($data);

    // Update the session's last_message_at timestamp
    ChatSession::where('id', $data['chat_session_id'])
      ->update(['last_message_at' => $message->created_at]);

    return $message;
  }

  /**
   * Get the count of unread messages for a specific reader type.
   *
   * @param int $sessionId
   * @param string $readerType Either 'user' or 'admin'
   * @return int
   */
  public function getUnreadMessagesCount(int $sessionId, string $readerType): int
  {
    // If reader is user, count unread admin messages
    // If reader is admin, count unread user messages
    $senderType = $readerType === 'user' ? 'admin' : 'user';

    return ChatMessage::where('chat_session_id', $sessionId)
      ->where('sender_type', $senderType)
      ->whereNull('read_at')
      ->count();
  }

  /**
   * Mark messages as read for a specific reader type.
   *
   * @param int $sessionId
   * @param string $readerType Either 'user' or 'admin'
   * @return int Number of messages marked as read
   */
  public function markMessagesAsRead(int $sessionId, string $readerType): int
  {
    // If reader is user, mark admin messages as read
    // If reader is admin, mark user messages as read
    $senderType = $readerType === 'user' ? 'admin' : 'user';

    return ChatMessage::where('chat_session_id', $sessionId)
      ->where('sender_type', $senderType)
      ->whereNull('read_at')
      ->update(['read_at' => now()]);
  }

  /**
   * Get all chat sessions ordered by most recent activity.
   * Eager loads user and unread counts to prevent N+1 queries.
   *
   * @return Collection
   */
  public function getAllSessionsOrderedByActivity(): Collection
  {
    return ChatSession::with('user')
      ->withUnreadCount()
      ->orderBy('last_message_at', 'desc')
      ->orderBy('created_at', 'desc')
      ->get();
  }

  /**
   * Get messages created since a specific timestamp.
   *
   * @param int $sessionId
   * @param Carbon $since
   * @return Collection
   */
  public function getMessagesSince(int $sessionId, Carbon $since): Collection
  {
    return ChatMessage::where('chat_session_id', $sessionId)
      ->where('created_at', '>', $since)
      ->orderBy('created_at', 'asc')
      ->get();
  }
}
