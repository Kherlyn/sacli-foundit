<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatMessage extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'chat_session_id',
    'sender_type',
    'sender_id',
    'message',
    'read_at',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'read_at' => 'datetime',
    ];
  }

  /**
   * Get the chat session that owns the message.
   */
  public function chatSession(): BelongsTo
  {
    return $this->belongsTo(ChatSession::class);
  }

  /**
   * Get the sender (User or Admin) of the message.
   */
  public function sender(): MorphTo
  {
    return $this->morphTo();
  }

  /**
   * Scope a query to only include unread messages.
   */
  public function scopeUnread($query)
  {
    return $query->whereNull('read_at');
  }

  /**
   * Scope a query to only include messages for a specific session.
   */
  public function scopeForSession($query, int $sessionId)
  {
    return $query->where('chat_session_id', $sessionId);
  }

  /**
   * Mark this message as read.
   */
  public function markAsRead(): void
  {
    if (is_null($this->read_at)) {
      $this->update(['read_at' => now()]);
    }
  }

  /**
   * Check if the message is from a user.
   */
  public function isFromUser(): bool
  {
    return $this->sender_type === 'user';
  }

  /**
   * Check if the message is from an admin.
   */
  public function isFromAdmin(): bool
  {
    return $this->sender_type === 'admin';
  }
}
