<?php

namespace Database\Factories;

use App\Enums\ImpressionType;
use App\Models\Doctor;
use App\Models\Order;
use App\Models\Subscriber;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Order::class;

    public function definition()
    {
        return [
            'subscriber_id' => \App\Models\Subscriber::factory(),
            'doctor_id' => \App\Models\Doctor::factory(),
            'type_id' => \App\Models\Type::factory(),
            'paid' => 0,
            'invoiced' => true,
            'cost' => fake()->numberBetween(100, 2000),
            'patient_name' => fake()->name(),
            'patient_id' => fake()->numerify('########'),
            'status' => 'pending',
            'impression_type' => 1,
        ];
    }
}
