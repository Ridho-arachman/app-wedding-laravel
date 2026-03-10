<?php
// database/factories/ItemFactory.php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        static $codeNumber = 1;
        return [
            "code" => "BRG-" . str_pad($codeNumber++, 3, "0", STR_PAD_LEFT),
            "name" => $this->faker->word(),
            "description" => $this->faker->sentence(),
            "category" => $this->faker->randomElement([
                "tenda",
                "rias",
                "dekorasi",
                "kursi",
                "piring",
            ]),
            "unit" => $this->faker->randomElement([
                "buah",
                "meter",
                "set",
                "orang",
            ]),
            "stock" => $this->faker->numberBetween(10, 100),
            "min_stock" => $this->faker->numberBetween(1, 10),
            "image" => null,
            "is_active" => true,
        ];
    }
}
