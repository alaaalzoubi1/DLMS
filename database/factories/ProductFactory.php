<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'category_id' => \App\Models\Category::factory(),
            'name' => fake()->word(),
            'price' => fake()->randomFloat(2, 10, 500),
            'is_deleted' => false,
        ];
    }
}
