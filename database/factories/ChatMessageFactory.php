<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatMessage>
 */
class ChatMessageFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = ChatMessage::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    // Default to user message
    $senderType = 'user';
    $senderId = User::factory();

    return [
      'chat_session_id' => ChatSession::factory(),
      'sender_type' => $senderType,
      'sender_id' => $senderId,
      'message' => fake()->sentence(),
      'read_at' => null,
    ];
  }

  /**
   * Indicate that the message is from a user.
   */
  public function fromUser(?User $user = null): static
  {
    return $this->state(function (array $attributes) use ($user) {
      return [
        'sender_type' => 'user',
        'sender_id' => $user?->id ?? User::factory(),
      ];
    });
  }

  /**
   * Indicate that the message is from an admin.
   */
  public function fromAdmin(?Admin $admin = null): static
  {
    return $this->state(function (array $attributes) use ($admin) {
      return [
        'sender_type' => 'admin',
        'sender_id' => $admin?->id ?? Admin::factory(),
      ];
    });
  }

  /**
   * Indicate that the message has been read.
   */
  public function read(): static
  {
    return $this->state(fn(array $attributes) => [
      'read_at' => fake()->dateTimeBetween('-1 day', 'now'),
    ]);
  }

  /**
   * Indicate that the message is unread.
   */
  public function unread(): static
  {
    return $this->state(fn(array $attributes) => [
      'read_at' => null,
    ]);
  }

  /**
   * Create a message with specific content.
   */
  public function withMessage(string $message): static
  {
    return $this->state(fn(array $attributes) => [
      'message' => $message,
    ]);
  }

  /**
   * Create a long message.
   */
  public function long(): static
  {
    return $this->state(fn(array $attributes) => [
      'message' => fake()->paragraphs(3, true),
    ]);
  }
}
