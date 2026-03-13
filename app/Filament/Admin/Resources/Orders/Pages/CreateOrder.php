<?php

namespace App\Filament\Admin\Resources\Orders\Pages;

use App\Filament\Admin\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageItem;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function handleRecordCreation(array $data): Order
    {
        // Generate order_number
        $year = date("Y");
        $lastOrder = Order::where("order_number", "like", "WO-{$year}-%")
            ->orderBy("order_number", "desc")
            ->first();
        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $newNumber = "0001";
        }
        $orderNumber = "WO-{$year}-{$newNumber}";

        // Ambil paket untuk menghitung ulang total
        $package = Package::find($data["package_code"]);
        if (!$package) {
            throw new \Exception("Paket tidak ditemukan");
        }

        // Hitung total harga: harga paket + biaya tambahan
        $additionalCharge = $data["additional_charge"] ?? 0;
        $totalPrice = $package->price + $additionalCharge;
        $dpAmount = (int) ($totalPrice * 0.5); // 50% dari total

        // Siapkan data untuk disimpan
        $orderData = [
            "order_number" => $orderNumber,
            "customer_name" => $data["customer_name"],
            "customer_phone" => $data["customer_phone"],
            "customer_address" => $data["customer_address"] ?? null,
            "event_date" => $data["event_date"],
            "package_code" => $data["package_code"],
            "total_price" => $totalPrice,
            "dp_amount" => $dpAmount,
            "additional_charge" => $additionalCharge,
            "charge_description" => $data["charge_description"] ?? null,
            "status" => "dp_paid", // default karena DP langsung dibayar saat order
            "notes" => $data["notes"] ?? null,
            "created_by" => Auth::id(),
        ];

        $order = Order::create($orderData);

        // Copy items dari package ke order_items
        $packageItems = PackageItem::with("item")
            ->where("package_code", $data["package_code"])
            ->get();

        foreach ($packageItems as $pkgItem) {
            $order->items()->create([
                "item_code" => $pkgItem->item_code,
                "quantity" => $pkgItem->quantity,
                "unit" => $pkgItem->unit ?? $pkgItem->item->unit,
                // Harga per unit bisa diisi 0 dulu, atau hitung proporsional
                "price_per_unit" => 0,
            ]);
        }

        return $order;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl("view", [
            "record" => $this->record,
        ]);
    }
}
