<?php

namespace Database\Factories;

use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class;

    public function definition()
    {
        return [
            'subscriber_id' => Subscriber::inRandomOrder()->first()->id ?? Subscriber::factory(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'FCM_token' => $this->faker->optional()->sha256,
            'is_available' => $this->faker->boolean(90),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'working_on' => $this->faker->numberBetween(0, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
