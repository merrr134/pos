<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id'  => Category::factory(),
            'name'         => fake()->words(2, true),
            'description'  => fake()->sentence(),
            'image'        => null,
            'price'        => fake()->numberBetween(10, 100) * 1000,
            'is_available' => true,
        ];
    }
}
