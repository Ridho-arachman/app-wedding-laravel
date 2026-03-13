<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat user admin (perbaikan: tambahkan '=>' dan hash password)
        $admin = User::firstOrCreate(
            ["email" => "admin@farisa.com"],
            [
                "name" => "Admin Farisa",
                "password" => Hash::make("password"), // atau bcrypt('password')
                "role" => "admin", // bisa diisi, tapi default sudah admin
            ],
        );

        // 2. Buat 25 item
        $items = Item::factory(25)->create();

        // 3. Buat 8 paket
        $packages = Package::factory(8)->create();

        // 4. Hubungkan paket dengan item (package_items)
        foreach ($packages as $package) {
            $randomItems = $items->random(rand(5, 15));
            foreach ($randomItems as $item) {
                PackageItem::factory()->create([
                    "package_code" => $package->code,
                    "item_code" => $item->code,
                    "unit" => $item->unit,
                ]);
            }
        }

        // 5. Buat 50 order
        $orders = Order::factory(50)->make();
        foreach ($orders as $order) {
            $package = $packages->random();
            $order->package_code = $package->code;
            $order->total_price = $package->price;
            $order->dp_amount = $package->price * 0.5;
            $order->created_by = $admin->id;
            $order->save();

            // 6. Buat order_items (copy dari package_items paket terkait)
            $packageItems = PackageItem::where(
                "package_code",
                $package->code,
            )->get();
            foreach ($packageItems as $pi) {
                OrderItem::factory()->create([
                    "order_number" => $order->order_number,
                    "item_code" => $pi->item_code,
                    "quantity" => $pi->quantity,
                    "unit" => $pi->unit,
                    "price_per_unit" => 0, // atau bisa diisi jika ada harga sewa
                ]);
            }

            // 7. Buat 0-4 pembayaran
            $paymentCount = rand(0, 4);
            for ($i = 0; $i < $paymentCount; $i++) {
                Payment::factory()->create([
                    "order_number" => $order->order_number,
                ]);
            }
        }

        // 8. Update status order berdasarkan total pembayaran (opsional)
        foreach (Order::all() as $order) {
            $totalPaid = $order->payments()->sum("amount");
            if ($totalPaid >= $order->total_price) {
                $order->status = "paid";
            } elseif ($totalPaid >= $order->dp_amount) {
                $order->status = "dp_paid";
            } elseif ($totalPaid > 0) {
                $order->status = "installment";
            }
            $order->saveQuietly();
        }
    }
}
