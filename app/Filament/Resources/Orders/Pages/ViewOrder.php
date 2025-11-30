<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getInfolist(): array
    {
        return [
            TextEntry::make("id")->label("Order ID"),

            TextEntry::make("customer_name")->label("Nama Pelanggan"),

            TextEntry::make("total_price")
                ->label("Total Harga")
                ->money("IDR", true),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make("download_invoice")
                ->label("🖨️ Download Invoice")
                ->url(fn($record) => route("invoice.pdf", $record))
                ->openUrlInNewTab()
                ->color("gray"),

            EditAction::make(),
        ];
    }
}
