<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("order_number")
                    ->label("No. Order")
                    ->searchable(),
                TextColumn::make("customer_name")
                    ->label("Customer")
                    ->searchable(),
                TextColumn::make("event_date")
                    ->label("Tanggal Acara")
                    ->date("d M Y")
                    ->sortable(),
                TextColumn::make("package.name")->label("Paket")->badge(),
                TextColumn::make("total_price")
                    ->label("Total")
                    ->money("IDR")
                    ->sortable(),
                BadgeColumn::make("status")
                    ->label("Status")
                    ->colors([
                        "warning" => "dp_paid",
                        "info" => "installment",
                        "success" => "paid",
                        "secondary" => "completed",
                        "danger" => "cancelled",
                    ])
                    ->formatStateUsing(
                        fn($state) => ucfirst(str_replace("_", " ", $state)),
                    ),
            ])
            ->filters([
                SelectFilter::make("status")->options([
                    "dp_paid" => "DP Lunas",
                    "installment" => "Cicil",
                    "paid" => "Lunas",
                    "completed" => "Selesai",
                    "cancelled" => "Batal",
                ]),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
