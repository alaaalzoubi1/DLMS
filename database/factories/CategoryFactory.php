<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'subscriber_id' => Subscriber::inRandomOrder()->first()->id ?? Subscriber::factory(),
            'is_deleted' => $this->faker->boolean(10), // 10% chance of being deleted
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
