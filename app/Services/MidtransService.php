<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
// use Midtrans\Notification;

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
        int $amount,
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
                "gross_amount" => $amount, // gunakan $amount, bukan $order->dp_amount
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
    public function handleNotification(string $notificationBody)
    {
        // Coba buat objek Notification, jika gagal karena masalah signature atau lainnya, fallback ke json_decode
        try {
            $notif = new \Midtrans\Notification($notificationBody);
            $isObject = true;
        } catch (\Exception $e) {
            // Fallback untuk testing: decode saja
            $notif = json_decode($notificationBody, true);
            $isObject = false;
        }

        if ($isObject) {
            $transaction = $notif->transaction_status;
            $orderId = $notif->order_id;
            $response = $notif->getResponse();
        } else {
            $transaction = $notif["transaction_status"];
            $orderId = $notif["order_id"];
            $response = $notif;
        }

        // Pisahkan order_number dan payment_id (format: orderNumber-paymentId)
        $hyphenParts = explode("-", $orderId);
        $last = array_pop($hyphenParts);
        if (strlen($last) === 26 && ctype_alnum($last)) {
            $paymentId = $last;
            $orderNumber = implode("-", $hyphenParts);
        } else {
            Log::error("Invalid order_id format", ["order_id" => $orderId]);
            return false;
        }

        $order = Order::where("order_number", $orderNumber)->first();
        if (!$order) {
            Log::error("Order not found", ["order_number" => $orderNumber]);
            return false;
        }

        $payment = Payment::find($paymentId);
        if (!$payment) {
            Log::error("Payment not found", ["payment_id" => $paymentId]);
            return false;
        }

        // Update payment
        $payment->midtrans_status = $transaction;
        $payment->midtrans_response = json_encode($response); // simpan sebagai JSON
        if ($transaction === "settlement" || $transaction === "capture") {
            $payment->payment_date = now(); // isi tanggal settlement
        }
        $payment->save();

        // Update status order (sama seperti kode Anda sebelumnya)
        $totalPaid = $order
            ->payments()
            ->where("method", "midtrans")
            ->whereIn("midtrans_status", ["settlement", "capture"])
            ->sum("amount");
        $totalPaid += $order
            ->payments()
            ->where("method", "cash")
            ->sum("amount");

        if ($totalPaid >= $order->total_price) {
            $order->status = "paid";
        } elseif ($totalPaid >= $order->dp_amount) {
            $order->status = "installment";
        } else {
            $order->status = "dp_paid";
        }
        $order->save();

        return true;
    }
}
