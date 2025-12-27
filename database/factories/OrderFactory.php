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
        $type = Type::inRandomOrder()->first() ?? Type::factory()->create();

        return [
            'doctor_id' => Doctor::inRandomOrder()->first()->id ?? Doctor::factory(),
            'subscriber_id' => $type->subscriber_id,
            'type_id' => $type->id,
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'paid' => $this->faker->numberBetween(0, 5000),
            'cost' => $this->faker->numberBetween(500, 10000),
            'patient_name' => $this->faker->name(),
            'receive' => null,
            'delivery' => null,
            'patient_id' => $this->faker->uuid(),
            'impression_type' => $this->faker->randomElement([
                ImpressionType::DIGITAL->value,
                ImpressionType::TRADITIONAL->value,
                ImpressionType::BOTH->value,
            ]),
        ];
    }
}
