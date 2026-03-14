<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = "Pendapatan Bulanan";

    protected function getData(): array
    {
        $data = Trend::query(
            Payment::where("method", "cash")->orWhere(function ($q) {
                $q->where("method", "midtrans")->whereIn("midtrans_status", [
                    "settlement",
                    "capture",
                ]);
            }),
        )
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->sum("amount");

        return [
            "datasets" => [
                [
                    "label" => "Pendapatan",
                    "data" => $data->map(
                        fn(TrendValue $value) => $value->aggregate,
                    ),
                ],
            ],
            "labels" => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return "line";
    }
}
