<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemImage>
 */
class ItemImageFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = ItemImage::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $originalName = $this->faker->word() . '.jpg';

    return [
      'item_id' => Item::factory(),
      'filename' => $this->faker->uuid() . '.jpg',
      'original_name' => $originalName,
      'mime_type' => 'image/jpeg',
      'size' => $this->faker->numberBetween(50000, 2000000), // 50KB to 2MB
    ];
  }

  /**
   * Indicate that the image is a PNG file.
   */
  public function png(): static
  {
    return $this->state(fn(array $attributes) => [
      'filename' => $this->faker->uuid() . '.png',
      'original_name' => $this->faker->word() . '.png',
      'mime_type' => 'image/png',
    ]);
  }

  /**
   * Indicate that the image is a GIF file.
   */
  public function gif(): static
  {
    return $this->state(fn(array $attributes) => [
      'filename' => $this->faker->uuid() . '.gif',
      'original_name' => $this->faker->word() . '.gif',
      'mime_type' => 'image/gif',
    ]);
  }
}
