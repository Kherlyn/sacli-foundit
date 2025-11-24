<?php

namespace Database\Factories;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatSession>
 */
class ChatSessionFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = ChatSession::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'status' => 'open',
      'last_message_at' => fake()->dateTimeBetween('-1 week', 'now'),
    ];
  }

  /**
   * Indicate that the chat session is closed.
   */
  public function closed(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'closed',
    ]);
  }

  /**
   * Indicate that the chat session has no messages yet.
   */
  public function withoutMessages(): static
  {
    return $this->state(fn(array $attributes) => [
      'last_message_at' => null,
    ]);
  }

  /**
   * Indicate that the chat session has recent activity.
   */
  public function recent(): static
  {
    return $this->state(fn(array $attributes) => [
      'last_message_at' => fake()->dateTimeBetween('-1 hour', 'now'),
    ]);
  }

  /**
   * Indicate that the chat session is old.
   */
  public function old(): static
  {
    return $this->state(fn(array $attributes) => [
      'last_message_at' => fake()->dateTimeBetween('-1 month', '-1 week'),
    ]);
  }
}
