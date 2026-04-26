<?php

namespace Database\Factories;

use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Order;
use App\Models\ToothColor;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderProductFactory extends Factory
{
    protected $model = OrderProduct::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'order_id' => Order::factory(),
            'tooth_color_id' => ToothColor::factory(),
            'tooth_numbers' => $this->faker->randomElements(range(1, 32), $this->faker->numberBetween(1, 5)),
            'specialization_users_id' => null,
            'note' => $this->faker->optional()->sentence(),
            'product_name' => $this->faker->words(3, true),
            'unit_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
