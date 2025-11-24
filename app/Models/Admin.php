<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'notification_preferences',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'notification_preferences' => 'array',
    ];
  }

  /**
   * Get the chat messages sent by this admin.
   */
  public function chatMessages(): MorphMany
  {
    return $this->morphMany(ChatMessage::class, 'sender');
  }

  /**
   * Get default notification preferences for admins.
   */
  public function getDefaultNotificationPreferences(): array
  {
    return [
      'admin_new_submissions' => true,
      'admin_queue_alerts' => true,
      'admin_system_events' => true,
      'admin_statistics' => false,
      'admin_chat_messages' => true,
    ];
  }

  /**
   * Get admin's notification preferences with defaults.
   */
  public function getNotificationPreferences(): array
  {
    return array_merge(
      $this->getDefaultNotificationPreferences(),
      $this->notification_preferences ?? []
    );
  }

  /**
   * Check if admin wants to receive a specific notification type.
   */
  public function wantsNotification(string $type): bool
  {
    $preferences = $this->getNotificationPreferences();
    return $preferences[$type] ?? false;
  }

  /**
   * Update notification preferences.
   */
  public function updateNotificationPreferences(array $preferences): void
  {
    $this->update([
      'notification_preferences' => array_merge(
        $this->getNotificationPreferences(),
        $preferences
      )
    ]);
  }
}
