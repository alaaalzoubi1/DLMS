<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['مجموعة', 'قسم']),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}
