<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Clinic',
            'has_special_price' => $this->faker->boolean(30),
            'tax_number' => $this->faker->boolean(50) ? $this->faker->numerify('TAX-######') : null,
            'clinic_code' => strtoupper($this->faker->unique()->bothify('CLI###')),
            'commercial_registration' => $this->faker->numerify('CR#######'),
        ];
    }
}
