<?php

namespace Database\Factories;

use App\Models\ToothColor;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToothColorFactory extends Factory
{
    protected $model = ToothColor::class;

    public function definition(): array
    {
        return [
            'color' => $this->faker->colorName(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}
