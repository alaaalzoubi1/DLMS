<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Order;
use App\Models\Subscriber;
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
            'subscriber_id' => Subscriber::factory(),
            'doctor_id' => Doctor::factory(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'type' => $this->faker->randomElement(['futures', 'new', 'test', 'returned']),
            'invoiced' => $this->faker->boolean,
            'paid' => $this->faker->numberBetween(40, 1500),
            'cost' => $this->faker->numberBetween(100, 5000),
            'patient_name' => $this->faker->name,
            'receive' => $this->faker->date,
            'delivery' => $this->faker->optional()->date,
            'patient_id' => $this->faker->uuid,
            'specialization' => $this->faker->word,
        ];
    }
}
