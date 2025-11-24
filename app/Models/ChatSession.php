<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'status',
    'last_message_at',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'last_message_at' => 'datetime',
    ];
  }

  /**
   * Get the user that owns the chat session.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get the messages for the chat session.
   */
  public function messages(): HasMany
  {
    return $this->hasMany(ChatMessage::class);
  }

  /**
   * Scope a query to only include open sessions.
   */
  public function scopeOpen($query)
  {
    return $query->where('status', 'open');
  }

  /**
   * Scope a query to include unread message count.
   */
  public function scopeWithUnreadCount($query)
  {
    return $query->withCount([
      'messages as unread_admin_messages_count' => function ($query) {
        $query->where('sender_type', 'admin')->whereNull('read_at');
      },
      'messages as unread_user_messages_count' => function ($query) {
        $query->where('sender_type', 'user')->whereNull('read_at');
      }
    ]);
  }

  /**
   * Get the count of unread admin messages.
   */
  public function getUnreadAdminMessagesCount(): int
  {
    return $this->messages()
      ->where('sender_type', 'admin')
      ->whereNull('read_at')
      ->count();
  }

  /**
   * Get the count of unread user messages.
   */
  public function getUnreadUserMessagesCount(): int
  {
    return $this->messages()
      ->where('sender_type', 'user')
      ->whereNull('read_at')
      ->count();
  }

  /**
   * Mark messages as read by the specified reader type.
   *
   * @param string $readerType Either 'user' or 'admin'
   */
  public function markMessagesAsRead(string $readerType): void
  {
    // If reader is user, mark admin messages as read
    // If reader is admin, mark user messages as read
    $senderType = $readerType === 'user' ? 'admin' : 'user';

    $this->messages()
      ->where('sender_type', $senderType)
      ->whereNull('read_at')
      ->update(['read_at' => now()]);
  }
}
