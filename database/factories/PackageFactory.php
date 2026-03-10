<?php
// database/factories/PackageFactory.php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition()
    {
        static $codeNumber = 1;
        return [
            "code" => "PKG-" . str_pad($codeNumber++, 3, "0", STR_PAD_LEFT),
            "name" => $this->faker->randomElement([
                "Paket Hemat",
                "Paket Silver",
                "Paket Gold",
                "Paket Platinum",
            ]),
            "description" => $this->faker->paragraph(),
            "price" => $this->faker->numberBetween(5_000_000, 20_000_000),
            "image" => null,
            "is_active" => true,
        ];
    }
}
