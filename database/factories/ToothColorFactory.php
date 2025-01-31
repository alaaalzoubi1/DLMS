<?php

namespace Database\Factories;

use App\Models\Subscriber;
use App\Models\ToothColor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ToothColor>
 */
class ToothColorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ToothColor::class;

    public function definition()
    {
        return [
            'subscriber_id' => Subscriber::factory(),
            'color' => $this->faker->safeColorName,
            'is_deleted' => false,
        ];
    }
}
