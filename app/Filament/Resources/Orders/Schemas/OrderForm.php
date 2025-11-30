<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Package;
use App\Models\Packages;
use Filament\Forms;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make("Data Customer")
                ->schema([
                    Forms\Components\TextInput::make("customer_name")
                        ->label("Nama Customer")
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make("customer_email")
                        ->label("Email")
                        ->email()
                        ->maxLength(255),

                    Forms\Components\TextInput::make("customer_phone")
                        ->label("No. HP")
                        ->tel()
                        ->maxLength(20),

                    Forms\Components\Textarea::make("notes")
                        ->label("Catatan Khusus")
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make("Paket & Harga")->schema([
                Forms\Components\Select::make("package_id")
                    ->label("Pilih Paket")
                    ->relationship("package", "name")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, Set $set) {
                        if ($state) {
                            $package = Packages::find($state);
                            $total = $package?->price ?? 0;
                            $dp = ceil($total * 0.5);
                            $set("total_price", $total);
                            $set("dp_amount", $dp);
                            $set("remaining_amount", $total - $dp);
                        }
                    }),

                Forms\Components\DatePicker::make("event_date")
                    ->label("Tanggal Acara")
                    ->required()
                    ->minDate(now()),

                Grid::make(3)->schema([
                    Forms\Components\TextInput::make("total_price")
                        ->label("Total Paket")
                        ->numeric()
                        ->prefix("Rp")
                        ->readOnly(),

                    Forms\Components\TextInput::make("dp_amount")
                        ->label("DP (50%)")
                        ->numeric()
                        ->prefix("Rp")
                        ->readOnly(),

                    Forms\Components\TextInput::make("remaining_amount")
                        ->label("Sisa")
                        ->numeric()
                        ->prefix("Rp")
                        ->readOnly(),
                ]),
            ]),

            Section::make("Pembayaran")->schema([
                Forms\Components\Select::make("status")
                    ->label("Status")
                    ->options([
                        "draft" => "Draft",
                        "dp_pending" => "Menunggu DP",
                        "dp_paid" => "DP Dibayar",
                        "full_pending" => "Menunggu Lunas",
                        "full_paid" => "Lunas",
                        "completed" => "Selesai",
                        "cancelled" => "Dibatalkan",
                    ])
                    ->default("draft")
                    ->required(),

                Grid::make(2)->schema([
                    Forms\Components\DatePicker::make("dp_paid_at")->label(
                        "Tanggal Bayar DP",
                    ),

                    Forms\Components\DatePicker::make("full_paid_at")->label(
                        "Tanggal Pelunasan",
                    ),
                ]),

                Fieldset::make("Virtual Account (Simulasi)")
                    ->schema([
                        Forms\Components\TextInput::make("va_number")
                            ->label("Nomor VA")
                            ->readOnly(),

                        Forms\Components\Select::make("va_bank")
                            ->label("Bank")
                            ->options([
                                "bca" => "BCA",
                                "mandiri" => "Mandiri",
                                "bni" => "BNI",
                                "bri" => "BRI",
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make("midtrans_order_id")
                            ->label("Order ID Midtrans")
                            ->readOnly(),
                        Forms\Components\Hidden::make("user_id")->default(
                            auth()->id(),
                        ),
                    ])
                    ->columns(3),
            ]),
        ]);
    }
}
