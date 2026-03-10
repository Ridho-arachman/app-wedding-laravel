<?php
// database/factories/PaymentFactory.php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            "order_number" => null, // diisi seeder
            "type" => $this->faker->randomElement([
                "dp",
                "installment",
                "final",
            ]),
            "amount" => $this->faker->numberBetween(1_000_000, 10_000_000),
            "payment_date" => $this->faker->dateTimeBetween("-1 month", "now"),
            "method" => $this->faker->randomElement([
                "cash",
                "transfer",
                "midtrans",
            ]),
            "proof" => null,
            "midtrans_order_id" => null,
            "midtrans_status" => null,
            "midtrans_response" => null,
            "notes" => $this->faker->optional()->sentence(),
        ];
    }
}
