<?php

namespace App\Filament\Admin\Resources\Orders\Pages;

use App\Filament\Admin\Resources\Orders\OrderResource;
use App\Services\MidtransService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Hanya menghitung pembayaran yang benar-benar sukses:
     * - Tunai langsung dihitung
     * - Midtrans hanya yang settlement/capture
     */
    protected function getTotalPaid(): int
    {
        $cashPaid = $this->record
            ->payments()
            ->where("method", "cash")
            ->sum("amount");

        $midtransPaid = $this->record
            ->payments()
            ->where("method", "midtrans")
            ->whereIn("midtrans_status", ["settlement", "capture"])
            ->sum("amount");

        return $cashPaid + $midtransPaid;
    }

    protected function getRemainingAmount(): int
    {
        return max(0, $this->record->total_price - $this->getTotalPaid());
    }

    protected function getDpRemaining(): int
    {
        return max(0, $this->record->dp_amount - $this->getTotalPaid());
    }

    protected function getPaymentTypeOptions(): array
    {
        $totalPaid = $this->getTotalPaid();
        $dpAmount = $this->record->dp_amount;
        $totalPrice = $this->record->total_price;

        if ($totalPaid == 0) {
            return [
                "dp" => "DP",
                "final" => "Lunas (Langsung)",
            ];
        } elseif ($totalPaid >= $dpAmount && $totalPaid < $totalPrice) {
            return [
                "installment" => "Cicilan",
                "final" => "Lunas",
            ];
        }

        return [];
    }

    protected function getDefaultAmountForType(?string $type): int
    {
        if (!$type) {
            return 0;
        }

        $totalPaid = $this->getTotalPaid();
        $dpAmount = $this->record->dp_amount;
        $totalPrice = $this->record->total_price;

        return match ($type) {
            "dp" => max(0, $dpAmount - $totalPaid),
            "installment", "final" => max(0, $totalPrice - $totalPaid),
            default => 0,
        };
    }

    protected function getMaxAmountForType(?string $type): int
    {
        return $this->getDefaultAmountForType($type);
    }

    protected function determinePaymentType(array $data): string
    {
        $selectedType = $data["payment_type"];
        $amount = $data["amount"];
        $totalPaid = $this->getTotalPaid();
        $dpAmount = $this->record->dp_amount;
        $totalPrice = $this->record->total_price;

        if ($selectedType === "dp") {
            if ($totalPaid >= $dpAmount) {
                throw new \Exception("DP sudah lunas, tidak bisa memilih DP");
            }
            if ($amount > $dpAmount - $totalPaid) {
                throw new \Exception("Jumlah melebihi sisa DP");
            }
            return "dp";
        }

        if ($selectedType === "installment") {
            if ($totalPaid < $dpAmount) {
                throw new \Exception(
                    "DP belum lunas, tidak bisa memilih cicilan",
                );
            }
            if ($totalPaid + $amount >= $totalPrice) {
                return "final";
            }
            return "installment";
        }

        if ($selectedType === "final") {
            return "final";
        }

        throw new \Exception("Tipe pembayaran tidak valid");
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make("pay_midtrans")
                ->label("Bayar via Midtrans")
                ->icon("heroicon-o-credit-card")
                ->color("success")
                ->visible(
                    fn() => in_array($this->record->status, [
                        "dp_paid",
                        "installment",
                    ]) && $this->getRemainingAmount() > 0,
                )
                ->form([
                    Select::make("payment_type")
                        ->label("Tipe Pembayaran")
                        ->options(fn() => $this->getPaymentTypeOptions())
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set(
                                "amount",
                                $this->getDefaultAmountForType($state),
                            );
                        }),
                    TextInput::make("amount")
                        ->label("Jumlah Dibayar")
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(
                            fn($get) => $this->getMaxAmountForType(
                                $get("payment_type"),
                            ),
                        ),
                ])
                ->action(function (array $data, MidtransService $midtrans) {
                    $type = $this->determinePaymentType($data);
                    $amount = $data["amount"];

                    $payment = $this->record->payments()->create([
                        "type" => $type,
                        "amount" => $amount,
                        "payment_date" => now(),
                        "method" => "midtrans",
                        "midtrans_status" => "pending",
                    ]);

                    $items = $this->record->items
                        ->map(
                            fn($item) => [
                                "id" => $item->item_code,
                                "name" => $item->item->name,
                                "price" => (int) $item->price_per_unit,
                                "quantity" => $item->quantity,
                            ],
                        )
                        ->toArray();

                    $customer = [
                        "first_name" => $this->record->customer_name,
                        "phone" => $this->record->customer_phone,
                        "address" => $this->record->customer_address,
                    ];

                    $result = $midtrans->createTransaction(
                        $this->record,
                        $items,
                        $customer,
                        $payment->id,
                        $amount,
                    );

                    if (isset($result["snap_token"])) {
                        session([
                            "snap_token" => $result["snap_token"],
                            "payment_amount" => $amount,
                        ]);
                        return redirect()->route("midtrans.pay", [
                            "order" => $this->record->order_number,
                        ]);
                    } else {
                        Notification::make()
                            ->title("Gagal membuat transaksi Midtrans")
                            ->body($result["error"] ?? "Unknown error")
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make("pay_cash")
                ->label("Bayar Tunai")
                ->icon("heroicon-o-banknotes")
                ->color("warning")
                ->visible(
                    fn() => in_array($this->record->status, [
                        "dp_paid",
                        "installment",
                    ]) && $this->getRemainingAmount() > 0,
                )
                ->form([
                    Select::make("payment_type")
                        ->label("Tipe Pembayaran")
                        ->options(fn() => $this->getPaymentTypeOptions())
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set(
                                "amount",
                                $this->getDefaultAmountForType($state),
                            );
                        }),
                    TextInput::make("amount")
                        ->label("Jumlah Dibayar")
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(
                            fn($get) => $this->getMaxAmountForType(
                                $get("payment_type"),
                            ),
                        ),
                    FileUpload::make("proof")
                        ->label("Bukti Transfer (foto)")
                        ->image()
                        ->directory("payments"),
                    Textarea::make("notes")->label("Catatan"),
                ])
                ->action(function (array $data) {
                    $type = $this->determinePaymentType($data);
                    $amount = $data["amount"];

                    $this->record->payments()->create([
                        "type" => $type,
                        "amount" => $amount,
                        "payment_date" => now(),
                        "method" => "cash",
                        "proof" => $data["proof"] ?? null,
                        "notes" => $data["notes"] ?? null,
                    ]);

                    $totalPaid = $this->getTotalPaid(); // sudah termasuk yang baru
                    $dpAmount = $this->record->dp_amount;
                    $totalPrice = $this->record->total_price;

                    if ($totalPaid >= $totalPrice) {
                        $this->record->update(["status" => "paid"]);
                    } elseif ($totalPaid >= $dpAmount) {
                        $this->record->update(["status" => "installment"]);
                    }

                    Notification::make()
                        ->title("Pembayaran berhasil dicatat")
                        ->success()
                        ->send();
                }),

            Actions\EditAction::make(),
        ];
    }
}
