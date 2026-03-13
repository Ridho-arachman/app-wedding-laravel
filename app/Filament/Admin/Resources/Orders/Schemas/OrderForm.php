<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use App\Models\Package;
use App\Models\PackageItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make("Pilih Paket")
                    ->schema([
                        Select::make("package_code")
                            ->label("Paket")
                            ->options(
                                Package::where("is_active", true)->pluck(
                                    "name",
                                    "code",
                                ),
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (
                                $state,
                                callable $set,
                            ) {
                                if ($state) {
                                    $package = Package::find($state);
                                    if ($package) {
                                        $set("total_price", $package->price);
                                        $set(
                                            "dp_amount",
                                            $package->price * 0.5,
                                        );
                                        $set("package_name", $package->name);
                                    }
                                }
                            }),
                        Placeholder::make("package_detail")
                            ->label("Detail Paket")
                            ->content(function (callable $get) {
                                $packageCode = $get("package_code");
                                if (!$packageCode) {
                                    return "Pilih paket terlebih dahulu";
                                }

                                $package = Package::find($packageCode);
                                if (!$package) {
                                    return "Paket tidak ditemukan";
                                }

                                // Ambil item dari package_item beserta relasi item
                                $packageItems = PackageItem::with("item")
                                    ->where("package_code", $packageCode)
                                    ->orderBy("sort_order")
                                    ->get();

                                $html = '<ul class="list-disc list-inside">';
                                foreach ($packageItems as $pkgItem) {
                                    $itemName = $pkgItem->item
                                        ? $pkgItem->item->name
                                        : "Item tidak ditemukan";
                                    $html .= "<li>{$itemName} - {$pkgItem->quantity} {$pkgItem->unit}</li>";
                                }
                                $html .= "</ul>";

                                return new HtmlString($html);
                            }),
                    ])
                    ->columns(2),

                Step::make("Data Customer")
                    ->schema([
                        TextInput::make("customer_name")
                            ->label("Nama Customer")
                            ->required(),
                        TextInput::make("customer_phone")
                            ->label("No. HP")
                            ->tel()
                            ->required(),
                        Textarea::make("customer_address")
                            ->label("Alamat")
                            ->rows(3),
                        DatePicker::make("event_date")
                            ->label("Tanggal Acara")
                            ->required()
                            ->minDate(now()->addDay()),
                    ])
                    ->columns(2),

                Step::make("Biaya Tambahan")->schema([
                    TextInput::make("additional_charge")
                        ->label("Biaya Tambahan (Rp)")
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(function (
                            $state,
                            callable $set,
                            callable $get,
                        ) {
                            $total = $get("total_price") ?: 0;
                            $set("total_price", $total + ($state ?: 0));
                            $set("dp_amount", ($total + ($state ?: 0)) * 0.5);
                        }),
                    TextInput::make("charge_description")
                        ->label("Keterangan Biaya")
                        ->placeholder("Misal: Luar kota, masuk gang, dll"),
                ]),

                Step::make("Ringkasan")->schema([
                    Placeholder::make("summary")
                        ->label("")
                        ->content(function (callable $get) {
                            $package = Package::find($get("package_code"));
                            $packageName = $package ? $package->name : "-";

                            // Ambil nilai asli
                            $totalValue = $get("total_price") ?: 0;
                            $dpValue = $get("dp_amount") ?: 0;
                            $chargeValue = $get("additional_charge") ?: 0;

                            // Hitung sisa tagihan (setelah DP)
                            $sisaValue = $totalValue - $dpValue;

                            // Format untuk tampilan
                            $total = number_format($totalValue, 0, ",", ".");
                            $dp = number_format($dpValue, 0, ",", ".");
                            $charge = number_format($chargeValue, 0, ",", ".");
                            $sisa = number_format($sisaValue, 0, ",", ".");

                            return new HtmlString("
                <div class='bg-gray-100 p-4 rounded'>
                    <p><strong>Paket:</strong> {$packageName}</p>
                    <p><strong>Total Harga:</strong> Rp {$total}</p>
                    <p><strong>Biaya Tambahan:</strong> Rp {$charge}</p>
                    <p><strong>DP 50%:</strong> Rp {$dp}</p>
                    <p><strong>Sisa Tagihan:</strong> Rp {$sisa}</p>
                    <p><strong>Customer:</strong> {$get(
                                "customer_name",
                            )} ({$get("customer_phone")})</p>
                    <p><strong>Tanggal Acara:</strong> {$get("event_date")}</p>
                </div>
            ");
                        }),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
