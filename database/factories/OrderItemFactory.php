<?php
namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            "order_number" => null,
            "item_code" => null,
            "quantity" => $this->faker->numberBetween(1, 10),
            "unit" => $this->faker->optional()->word(),
            "price_per_unit" => $this->faker->numberBetween(10000, 500000),
        ];
    }
}
