<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Doctor_Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor_Account>
 */
class Doctor_AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Doctor_Account::class;

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('secret'),
            'FCM_token' => null,
            'doctor_id' => function () {
                return Doctor::factory()->create()->id;
            },
        ];
    }
}
