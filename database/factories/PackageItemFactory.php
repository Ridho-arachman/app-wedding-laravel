<?php
namespace Database\Factories;

use App\Models\PackageItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageItemFactory extends Factory
{
    protected $model = PackageItem::class;

    public function definition()
    {
        return [
            "package_code" => null, // akan diisi di seeder
            "item_code" => null, // akan diisi di seeder
            "quantity" => $this->faker->numberBetween(1, 20),
            "unit" => $this->faker->optional()->word(),
            "sort_order" => $this->faker->numberBetween(0, 100),
        ];
    }
}
