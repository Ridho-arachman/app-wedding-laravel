<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected int|string|array $columnSpan = "full";

    public function table(Table $table): Table
    {
        return $table->query(Order::query()->latest()->limit(5))->columns([
            TextColumn::make("order_number")->label("No. Order")->searchable(),
            TextColumn::make("customer_name")->label("Customer"),
            TextColumn::make("event_date")->date("d M Y"),
            BadgeColumn::make("status")->colors([
                "warning" => "dp_paid",
                "info" => "installment",
                "success" => "paid",
                "secondary" => "completed",
                "danger" => "cancelled",
            ]),
            TextColumn::make("total_price")->money("IDR"),
        ]);
    }
}
