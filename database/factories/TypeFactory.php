<?php

namespace Database\Factories;

use App\Models\Type;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeFactory extends Factory
{
    protected $model = Type::class;

    public function definition(): array
    {
        return [
            'subscriber_id' => Subscriber::factory(),
            'type' => $this->faker->randomElement([1,2,3]),
            'invoiced' => $this->faker->boolean(70),
        ];
    }
}
