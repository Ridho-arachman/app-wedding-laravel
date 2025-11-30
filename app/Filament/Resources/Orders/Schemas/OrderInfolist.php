<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make("Data Customer")
                ->schema([
                    Infolists\Components\TextEntry::make(
                        "customer_name",
                    )->label("Nama"),

                    Infolists\Components\TextEntry::make(
                        "customer_email",
                    )->label("Email"),

                    Infolists\Components\TextEntry::make(
                        "customer_phone",
                    )->label("No. HP"),

                    Infolists\Components\TextEntry::make("notes")
                        ->label("Catatan")
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make("Detail Pesanan")->schema([
                Infolists\Components\TextEntry::make("order_code")->label(
                    "Kode Pesanan",
                ),

                Infolists\Components\TextEntry::make("package.name")->label(
                    "Paket",
                ),

                Infolists\Components\TextEntry::make("total_price")
                    ->label("Total")
                    ->money("IDR", divideBy: 1),

                Infolists\Components\TextEntry::make("dp_amount")
                    ->label("DP (50%)")
                    ->money("IDR", divideBy: 1),

                Infolists\Components\TextEntry::make("remaining_amount")
                    ->label("Sisa")
                    ->money("IDR", divideBy: 1),

                Infolists\Components\TextEntry::make("event_date")
                    ->label("Tanggal Acara")
                    ->date(),

                Infolists\Components\TextEntry::make("days_to_event")
                    ->label("H-")
                    ->badge()
                    ->color(
                        fn(int $state): string => $state < 0
                            ? "danger"
                            : ($state <= 7
                                ? "warning"
                                : "success"),
                    ),
            ]),

            Section::make("Pembayaran")->schema([
                Infolists\Components\TextEntry::make("status")
                    ->label("Status")
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            "draft" => "gray",
                            "dp_pending", "full_pending" => "warning",
                            "dp_paid" => "info",
                            "full_paid" => "success",
                            "completed" => "purple",
                            "cancelled" => "danger",
                            default => "gray",
                        },
                    ),

                Infolists\Components\TextEntry::make("dp_paid_at")
                    ->label("Tanggal DP")
                    ->date(),

                Infolists\Components\TextEntry::make("full_paid_at")
                    ->label("Tanggal Lunas")
                    ->date(),

                Section::make("Virtual Account")
                    ->schema([
                        Infolists\Components\TextEntry::make(
                            "va_number",
                        )->label("Nomor VA"),

                        Infolists\Components\TextEntry::make("va_bank")
                            ->label("Bank")
                            ->badge(),

                        Infolists\Components\TextEntry::make(
                            "transaction_status",
                        )
                            ->label("Status Transaksi")
                            ->badge()
                            ->color(
                                fn(string $state): string => match ($state) {
                                    "settle" => "success",
                                    "pending" => "warning",
                                    "expire", "cancel" => "danger",
                                    default => "gray",
                                },
                            ),
                    ])
                    ->columns(3),
            ]),
        ]);
    }
}
