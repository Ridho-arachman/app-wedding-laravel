<?php
// database/factories/OrderFactory.php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        static $orderNumberSequence = 1;
        $year = date("Y");
        $prefix = "WO-" . $year . "-";
        $orderNumber =
            $prefix . str_pad($orderNumberSequence++, 4, "0", STR_PAD_LEFT);

        return [
            "order_number" => $orderNumber,
            "customer_name" => $this->faker->name(),
            "customer_phone" => $this->faker->phoneNumber(),
            "customer_address" => $this->faker->address(),
            "event_date" => $this->faker->dateTimeBetween(
                "+1 month",
                "+6 months",
            ),
            "package_code" => null, // akan diisi di seeder
            "total_price" => $this->faker->numberBetween(5_000_000, 20_000_000),
            "dp_amount" => fn($attrs) => $attrs["total_price"] * 0.5,
            "status" => $this->faker->randomElement([
                "draft",
                "dp_pending",
                "dp_paid",
                "paid",
                "completed",
            ]),
            "notes" => $this->faker->optional()->sentence(),
            "created_by" => 1, // sementara, akan di-overwrite seeder
        ];
    }
}
