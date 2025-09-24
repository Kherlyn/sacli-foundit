<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Notifications\ItemVerifiedNotification;
use App\Notifications\ItemRejectedNotification;
use App\Notifications\ItemResolvedNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
  /**
   * Send item verification notification to user.
   */
  public function sendItemVerifiedNotification(Item $item): void
  {
    try {
      $item->user->notify(new ItemVerifiedNotification($item));

      Log::info('Item verified notification sent', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'item_title' => $item->title,
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to send item verified notification', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Send item rejection notification to user.
   */
  public function sendItemRejectedNotification(Item $item): void
  {
    try {
      $item->user->notify(new ItemRejectedNotification($item));

      Log::info('Item rejected notification sent', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'item_title' => $item->title,
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to send item rejected notification', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Send item resolved notification to user.
   */
  public function sendItemResolvedNotification(Item $item): void
  {
    try {
      $item->user->notify(new ItemResolvedNotification($item));

      Log::info('Item resolved notification sent', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'item_title' => $item->title,
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to send item resolved notification', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Send verification confirmation notification.
   */
  public function sendVerificationConfirmation(Item $item): void
  {
    try {
      // Send confirmation email to user about successful submission
      $item->user->notify(new \App\Notifications\ItemSubmissionConfirmation($item));

      Log::info('Item submission confirmation sent', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'item_title' => $item->title,
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to send submission confirmation', [
        'item_id' => $item->id,
        'user_id' => $item->user_id,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Update user notification preferences.
   */
  public function updateUserNotificationPreferences(User $user, array $preferences): void
  {
    try {
      $user->updateNotificationPreferences($preferences);

      Log::info('User notification preferences updated', [
        'user_id' => $user->id,
        'preferences' => $preferences,
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to update notification preferences', [
        'user_id' => $user->id,
        'error' => $e->getMessage(),
      ]);

      throw $e;
    }
  }

  /**
   * Get user's current notification preferences.
   */
  public function getUserNotificationPreferences(User $user): array
  {
    return $user->getNotificationPreferences();
  }
}
