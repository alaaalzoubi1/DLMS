<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clinic>
 */
class ClinicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'has_special_price' => $this->faker->boolean(30),
            'tax_number' => $this->faker->optional()->numerify('TX#########'),
            'clinic_code' => Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
