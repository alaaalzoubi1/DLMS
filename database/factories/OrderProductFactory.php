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
            'note' => $this->faker->optional()->sentence(),
            'tooth_number' => $this->faker->optional()->randomElement(['A1', 'B2', 'C3', 'D4']), // Example tooth numbers
            'product_id' => Product::factory(),
            'order_id' => Order::factory(),
            'tooth_color_id' => ToothColor::factory(),
            'specialization_users_id' => Specialization_User::factory(),
            'status' => $this->faker->randomElement(['working', 'finished'])
        ];
    }
}
