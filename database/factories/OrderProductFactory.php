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

            'tooth_numbers' => $this->faker->randomElements(
                ['A1', 'B2', 'C3', 'D4', 'E5', 'F6'],
                rand(1, 4)
            ),

            'product_id' => Product::inRandomOrder()->value('id') ?? Product::factory(),
            'unit_price' => Product::inRandomOrder()->value('price'),
            'product_name' => Product::inRandomOrder()->value('name'),
            'order_id' => Order::inRandomOrder()->value('id') ?? Order::factory(),
            'tooth_color_id' => ToothColor::inRandomOrder()->value('id') ?? ToothColor::factory(),
            'specialization_users_id' => Specialization_User::inRandomOrder()->value('id') ?? Specialization_User::factory(),

            'status' => $this->faker->randomElement(['working', 'finished']),
        ];
    }

}
