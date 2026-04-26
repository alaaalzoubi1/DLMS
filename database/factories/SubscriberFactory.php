<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'company_code' => strtoupper($this->faker->unique()->bothify('??###')),
            'trial_start_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'trial_end_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'tax_number' => $this->faker->boolean(70) ? $this->faker->numerify('VAT-########') : null,
            'country_code' => $this->faker->randomElement(['SA','SY']),
            'commercial_registration' => $this->faker->numerify('CR########'),
        ];
    }
}
