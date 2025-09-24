<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Item::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $types = ['lost', 'found'];
    $statuses = ['pending', 'verified', 'rejected', 'resolved'];
    $type = $types[array_rand($types)];
    $status = $statuses[array_rand($statuses)];

    return [
      'title' => fake()->sentence(3),
      'description' => fake()->paragraph(3),
      'category_id' => Category::factory(),
      'type' => $type,
      'status' => $status,
      'location' => fake()->address(),
      'date_occurred' => fake()->dateTimeBetween('-30 days', 'now'),
      'contact_info' => [
        'method' => ['email', 'phone', 'both'][array_rand(['email', 'phone', 'both'])],
        'email' => fake()->email(),
        'phone' => fake()->phoneNumber(),
      ],
      'user_id' => User::factory(),
      'admin_notes' => $status === 'rejected' ? fake()->sentence() : null,
      'verified_at' => $status === 'verified' ? fake()->dateTimeBetween('-7 days', 'now') : null,
      'resolved_at' => $status === 'resolved' ? fake()->dateTimeBetween('-3 days', 'now') : null,
    ];
  }

  /**
   * Indicate that the item is pending verification.
   */
  public function pending(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'pending',
      'verified_at' => null,
      'resolved_at' => null,
      'admin_notes' => null,
    ]);
  }

  /**
   * Indicate that the item is verified.
   */
  public function verified(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'verified',
      'verified_at' => fake()->dateTimeBetween('-7 days', 'now'),
      'resolved_at' => null,
    ]);
  }

  /**
   * Indicate that the item is rejected.
   */
  public function rejected(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'rejected',
      'verified_at' => null,
      'resolved_at' => null,
      'admin_notes' => fake()->sentence(),
    ]);
  }

  /**
   * Indicate that the item is resolved.
   */
  public function resolved(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'resolved',
      'verified_at' => fake()->dateTimeBetween('-7 days', 'now'),
      'resolved_at' => fake()->dateTimeBetween('-3 days', 'now'),
    ]);
  }

  /**
   * Indicate that the item is a lost item.
   */
  public function lost(): static
  {
    return $this->state(fn(array $attributes) => [
      'type' => 'lost',
    ]);
  }

  /**
   * Indicate that the item is a found item.
   */
  public function found(): static
  {
    return $this->state(fn(array $attributes) => [
      'type' => 'found',
    ]);
  }
}
