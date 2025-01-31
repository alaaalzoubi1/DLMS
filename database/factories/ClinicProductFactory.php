<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\ClinicProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClinicProduct>
 */
class ClinicProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ClinicProduct::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(), // Automatically create a product if not provided
            'clinic_id' => Clinic::factory(),   // Automatically create a clinic if not provided
            'price' => $this->faker->randomFloat(2, 50, 1000), // Random price between 50 and 1000
        ];
    }
}
