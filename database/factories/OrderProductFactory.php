<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\ToothColor;
use App\Models\Specialization_User;

class OrderProductFactory extends Factory
{
    protected $model = OrderProduct::class;

    public function definition()
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'product_id' => \App\Models\Product::factory(),
            'unit_price' => fake()->randomFloat(2, 10, 500),
            'product_name' => fake()->word(),
            'tooth_numbers' => json_encode([11,12]),
            'tooth_color_id' => \App\Models\ToothColor::factory(),
            'specialization_users_id' => null,
            'status' => 'working',
        ];
    }

}
