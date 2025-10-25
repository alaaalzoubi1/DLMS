<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor_Account>
 */
class DoctorAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'doctor_id' => Doctor::inRandomOrder()->first()->id ?? Doctor::factory(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 12345678,
            'FCM_token' => $this->faker->optional()->sha256,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
