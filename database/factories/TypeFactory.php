<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Type>
 */
class TypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'subscriber_id' => Subscriber::inRandomOrder()->first()->id ?? Subscriber::factory(),
            'type' => $this->faker->randomElement(['futures', 'new', 'test', 'returned']),
            'invoiced' => $this->faker->boolean(),
        ];
    }
}
