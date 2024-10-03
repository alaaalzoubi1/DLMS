<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model =  Subscriber::class;

    public function definition()
    {
        return [
            'company_name' => $this->faker->company,
            'company_code' => $this->faker->unique()->bothify('???????'),
            'trial_start_at' => now()->addDays(rand(30, 90)),
            'trial_end_at' => now()->addMonths(rand(6, 12)),
        ];
    }
}
