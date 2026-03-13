<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        static $orderNumberSequence = 1;
        $year = date("Y");
        $orderNumber =
            "WO-" .
            $year .
            "-" .
            str_pad($orderNumberSequence++, 4, "0", STR_PAD_LEFT);

        // Ambil package secara acak (atau buat baru)
        $package =
            Package::inRandomOrder()->first() ?? Package::factory()->create();
        $totalPrice =
            $package->price +
            $this->faker->optional(0.3)->numberBetween(100_000, 1_000_000) ?:
            0;

        return [
            "order_number" => $orderNumber,
            "customer_name" => $this->faker->name(),
            "customer_phone" => $this->faker->phoneNumber(),
            "customer_address" => $this->faker->address(),
            "event_date" => $this->faker->dateTimeBetween(
                "+1 month",
                "+6 months",
            ),
            "package_code" => $package->code,
            "total_price" => $totalPrice,
            "dp_amount" => (int) ($totalPrice * 0.5), // 50% dari total
            "additional_charge" => $totalPrice - $package->price,
            "charge_description" => $this->faker->optional(0.3)->sentence(),
            "status" => $this->faker->randomElement([
                "dp_paid",
                "installment",
                "paid",
                "completed",
                "cancelled",
            ]),
            "notes" => $this->faker->optional()->paragraph(),
            "created_by" => User::factory(), // atau ambil user existing
        ];
    }
}
