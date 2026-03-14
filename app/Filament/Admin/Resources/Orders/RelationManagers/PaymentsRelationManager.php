<?php

namespace App\Filament\Admin\Resources\Orders\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = "payments";

    protected static ?string $title = "Riwayat Pembayaran";

    public function table(Table $table): Table
    {
        return $table
            // Hapus modifyQueryUsing agar semua data payment ditampilkan
            ->columns([
                TextColumn::make("type")->label("Tipe")->badge()->color(
                    fn(string $state): string => match ($state) {
                        "dp" => "warning",
                        "installment" => "info",
                        "final" => "success",
                    },
                ),
                TextColumn::make("amount")->money("IDR")->sortable(),
                TextColumn::make("method")->badge(),
                TextColumn::make("payment_date")
                    ->dateTime("d M Y H:i")
                    ->sortable(),
                TextColumn::make("midtrans_status")
                    ->label("Midtrans Status")
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            "pending" => "warning",
                            "settlement" => "success",
                            "expire", "deny" => "danger",
                            default => "secondary",
                        },
                    ),
            ])
            ->filters([
                SelectFilter::make("method")->options([
                    "cash" => "Tunai",
                    "transfer" => "Transfer",
                    "midtrans" => "Midtrans",
                ]),
                // Filter tambahan untuk status Midtrans (opsional)
                SelectFilter::make("midtrans_status")
                    ->label("Status Midtrans")
                    ->options([
                        "pending" => "Pending",
                        "settlement" => "Settlement",
                        "expire" => "Expire",
                        "deny" => "Deny",
                    ])
                    ->visible(fn() => true), // tampilkan filter ini
            ])
            ->headerActions([]) // Kosongkan jika tidak ada
            ->recordActions([
                ViewAction::make()
                    ->modalHeading("Detail Pembayaran")
                    ->infolist([
                        Section::make("Informasi Pembayaran")->schema([
                            TextEntry::make("type")->label("Tipe"),
                            TextEntry::make("amount")->money("IDR"),
                            TextEntry::make("method")->label("Metode"),
                            TextEntry::make("payment_date")->dateTime(
                                "d M Y H:i",
                            ),
                            TextEntry::make("midtrans_status")->label(
                                "Status Midtrans",
                            ),
                            TextEntry::make("proof")
                                ->label("Bukti")
                                ->url(
                                    fn($record) => $record->proof
                                        ? asset("storage/" . $record->proof)
                                        : null,
                                )
                                ->openUrlInNewTab()
                                ->visible(
                                    fn($record) => $record->method === "cash" &&
                                        $record->proof,
                                ),
                            TextEntry::make("notes")->label("Catatan"),
                        ]),
                    ]),
            ]);
    }
}
