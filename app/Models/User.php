<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'notification_preferences',
        'course',
        'year',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
     * Get the items for the user.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a regular user.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Validation rules for course and year fields.
     */
    public static function courseYearRules(): array
    {
        return [
            'course' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1|max:6',
        ];
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include regular users.
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Get default notification preferences.
     */
    public function getDefaultNotificationPreferences(): array
    {
        $preferences = [
            'item_verified' => true,
            'item_rejected' => true,
            'item_resolved' => false,
            'admin_updates' => false,
        ];

        // Add admin-specific preferences for admin users
        if ($this->isAdmin()) {
            $preferences = array_merge($preferences, [
                'admin_new_submissions' => true,
                'admin_queue_alerts' => true,
                'admin_system_events' => true,
                'admin_statistics' => false,
            ]);
        }

        return $preferences;
    }

    /**
     * Get user's notification preferences with defaults.
     */
    public function getNotificationPreferences(): array
    {
        return array_merge(
            $this->getDefaultNotificationPreferences(),
            $this->notification_preferences ?? []
        );
    }

    /**
     * Check if user wants to receive a specific notification type.
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
