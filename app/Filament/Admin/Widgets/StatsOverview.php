<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $completedOrders = Order::where("status", "completed")->count();
        $totalRevenue =
            Payment::where("method", "cash")->sum("amount") +
            Payment::where("method", "midtrans")
                ->whereIn("midtrans_status", ["settlement", "capture"])
                ->sum("amount");
        $pendingMidtrans = Payment::where("method", "midtrans")
            ->where("midtrans_status", "pending")
            ->count();

        return [
            Stat::make("Total Order", $totalOrders)
                ->description("Semua order")
                ->color("primary")
                ->icon("heroicon-o-shopping-cart"),
            Stat::make("Order Selesai", $completedOrders)
                ->description("Status completed")
                ->color("success")
                ->icon("heroicon-o-check-circle"),
            Stat::make(
                "Total Pemasukan",
                "Rp " . number_format($totalRevenue, 0, ",", "."),
            )
                ->description("Cash + Midtrans settlement")
                ->color("info")
                ->icon("heroicon-o-currency-dollar"),
            Stat::make("Pending Midtrans", $pendingMidtrans)
                ->description("Menunggu konfirmasi")
                ->color("warning")
                ->icon("heroicon-o-clock"),
        ];
    }
}
