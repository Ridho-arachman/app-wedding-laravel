<?php

namespace App\Filament\Admin\Resources\Orders\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
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
            ->modifyQueryUsing(
                fn($query) => $query->where(function ($q) {
                    $q->where("method", "cash")->orWhere(function ($q2) {
                        $q2->where("method", "midtrans")->whereIn(
                            "midtrans_status",
                            ["settlement", "capture"],
                        );
                    });
                }),
            )
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
            ])
            ->headerActions([]) // Kosongkan jika tidak ada
            ->recordActions([ViewAction::make()]);
    }
}
