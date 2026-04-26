<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\ToothColor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'category_id' => Category::factory(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
