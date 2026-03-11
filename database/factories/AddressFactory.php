<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'street' => fake()->streetName(),
            'building_number' => fake()->buildingNumber(),
            'additional_number' => fake()->optional()->numerify('###'),
            'district' => fake()->citySuffix(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'locationAddress' => fake()->address(),
        ];
    }
}
