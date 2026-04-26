<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('12345678'),
            'subscriber_id' => Subscriber::factory(),
            'FCM_token' => $this->faker->optional()->sha256(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {

            $user->assignRole('admin');

        });
    }
}
