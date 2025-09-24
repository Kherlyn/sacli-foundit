<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Category::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $categories = ['Electronics', 'Clothing', 'Documents', 'Keys', 'Jewelry', 'Books', 'Sports', 'Bags', 'Accessories', 'Other'];
    $icons = ['phone', 'shirt', 'document', 'key', 'gem', 'book', 'ball', 'briefcase', 'heart', 'question'];

    return [
      'name' => $categories[array_rand($categories)] . ' ' . rand(100, 999),
      'description' => fake()->sentence(),
      'icon' => $icons[array_rand($icons)],
      'color' => '#10B981', // Default green color
    ];
  }
}
