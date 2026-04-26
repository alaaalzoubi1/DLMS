<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Doctor;
use App\Models\Subscriber;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');

        return [
            'doctor_id' => Doctor::factory(),
            'subscriber_id' => Subscriber::factory(),
            'type_id' => Type::factory(),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'invoiced' => $this->faker->boolean(80),
            'paid' => $this->faker->boolean(50),
            'cost' => $this->faker->randomFloat(2, 50, 5000),
            'patient_name' => $this->faker->name(),
            'receive' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'delivery' => $this->faker->dateTimeBetween('now', '+1 month'),
            'patient_id' => $this->faker->numerify('PAT###'),
            'impression_type' => $this->faker->randomElement([1,2,3]),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}
