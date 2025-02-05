<?php

namespace Database\Factories;

use App\Models\Specialization;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialization_Subscriber>
 */
class Specialization_SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'specializations_id' => Specialization::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}
