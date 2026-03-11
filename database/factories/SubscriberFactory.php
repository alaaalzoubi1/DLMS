<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class SubscriberFactory extends Factory
{
    public function definition()
    {
        return [
            'company_name' => fake()->company(),
            'company_code' => fake()->unique()->bothify('COMP-####'),
            'trial_start_at' => now(),
            'trial_end_at' => now()->addMonth(),
            'tax_number' => fake()->unique()->numerify('##########'),
            'commercial_registration' => fake()->unique()->numerify('########'),
            'country_code' => 'SA',
        ];
    }
}
