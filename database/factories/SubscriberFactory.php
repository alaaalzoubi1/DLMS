<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class SubscriberFactory extends Factory
{
    public function definition()
    {
        return [
            'company_name' => $this->faker->company,
            'company_code' => strtoupper($this->faker->unique()->bothify('???####')),
            'trial_start_at' => now(),
            'trial_end_at' => now()->addDays(rand(7, 30)),
            'tax_number' => $this->faker->optional()->numerify('TX#########'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
