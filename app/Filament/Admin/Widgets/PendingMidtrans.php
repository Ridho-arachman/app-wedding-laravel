<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingMidtransWidget extends BaseWidget
{
    protected int|string|array $columnSpan = "full";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->where("method", "midtrans")
                    ->where("midtrans_status", "pending")
                    ->with("order")
                    ->latest()
                    ->limit(5),
            )
            ->columns([
                TextColumn::make("order.order_number")
                    ->label("No. Order")
                    ->searchable(),
                TextColumn::make("amount")->money("IDR"),
                BadgeColumn::make("type")->colors([
                    "warning" => "dp",
                    "info" => "installment",
                    "success" => "final",
                ]),
                TextColumn::make("created_at")
                    ->label("Dibuat")
                    ->dateTime("d M Y H:i"),
            ]);
    }
}
