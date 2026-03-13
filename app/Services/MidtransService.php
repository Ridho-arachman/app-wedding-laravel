<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config("midtrans.server_key");
        Config::$isProduction = config("midtrans.is_production", false);
        Config::$isSanitized = config("midtrans.is_sanitized", true);
        Config::$is3ds = config("midtrans.is_3ds", true);
    }

    /**
     * Buat transaksi Snap dan kembalikan snap token
     */
    public function createTransaction(
        Order $order,
        array $items,
        array $customer,
        $paymentId,
    ) {
        Log::info("Midtrans gross_amount", [
            "order_number" => $order->order_number,
            "dp_amount_raw" => $order->dp_amount,
            "dp_amount_int" => (int) $order->dp_amount,
            "dp_amount_type" => gettype($order->dp_amount),
        ]);

        if ($order->dp_amount <= 0) {
            return [
                "error" =>
                    "DP tidak boleh 0. Pastikan order memiliki total harga yang valid.",
            ];
        }

        $orderId = $order->order_number . "-" . $paymentId;

        $params = [
            "transaction_details" => [
                "order_id" => $orderId,
                "gross_amount" => (int) $order->dp_amount,
            ],
            // "item_details" => $items,
            "customer_details" => [
                "first_name" =>
                    $customer["first_name"] ?? $order->customer_name,
                "phone" => $customer["phone"] ?? $order->customer_phone,
                "billing_address" => [
                    "address" =>
                        $customer["address"] ?? $order->customer_address,
                ],
            ],
            "callbacks" => [
                "finish" => route("midtrans.finish"),
            ],
        ];

        Log::info("Midtrans params", $params);

        try {
            $snapToken = Snap::getSnapToken($params);
            return ["snap_token" => $snapToken];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /**
     * Handle notifikasi dari Midtrans
     */
    public function handleNotification($notif)
    {
        $notif = new Notification();
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        // orderId format: ORDER_NUMBER-PAYMENT_ID
        $parts = explode("-", $orderId);
        $orderNumber = $parts[0];
        $paymentId = $parts[1] ?? null;

        $order = Order::where("order_number", $orderNumber)->first();
        if (!$order) {
            return false;
        }

        $payment = Payment::find($paymentId);
        if (!$payment) {
            return false;
        }

        // Update payment
        $payment->midtrans_status = $transaction;
        $payment->midtrans_response = json_encode($notif);
        $payment->save();

        // Update status order berdasarkan total pembayaran
        $totalPaid = $order
            ->payments()
            ->where("method", "midtrans")
            ->where("midtrans_status", "settlement")
            ->sum("amount");
        $totalPaid += $order
            ->payments()
            ->where("method", "cash")
            ->sum("amount");

        if ($transaction == "settlement" || $transaction == "capture") {
            if ($totalPaid >= $order->total_price) {
                $order->status = "paid";
            } elseif ($totalPaid >= $order->dp_amount) {
                $order->status = "installment";
            } else {
                $order->status = "dp_paid";
            }
        } elseif ($transaction == "pending") {
            // status tetap, mungkin nanti kita update
        } elseif (in_array($transaction, ["deny", "expire", "cancel"])) {
            // payment gagal, tidak ubah status order
        }

        $order->save();

        return true;
    }
}
