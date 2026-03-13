<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    /**
     * Menampilkan halaman pembayaran dengan snap token
     */
    public function pay($orderNumber)
    {
        $order = Order::where("order_number", $orderNumber)->firstOrFail();
        $snapToken = session("snap_token");
        $amount = session("payment_amount");

        if (!$snapToken) {
            return redirect()
                ->back()
                ->with("error", "Token pembayaran tidak ditemukan.");
        }

        return view("midtrans.pay", compact("order", "snapToken", "amount"));
    }

    /**
     * Halaman setelah pembayaran selesai (redirect dari Midtrans)
     */
    public function finish(Request $request)
    {
        $orderId = $request->get("order_id");

        // Coba separator titik terlebih dahulu
        $parts = explode(".", $orderId);
        if (count($parts) === 2) {
            $orderNumber = $parts[0];
            $paymentId = $parts[1];
        } else {
            // Fallback ke separator hubung (untuk transaksi lama)
            $hyphenParts = explode("-", $orderId);
            $last = array_pop($hyphenParts);
            if (strlen($last) === 26 && ctype_alnum($last)) {
                $paymentId = $last;
                $orderNumber = implode("-", $hyphenParts);
            } else {
                return redirect()
                    ->route("filament.admin.resources.orders.index")
                    ->with("error", "Format order ID tidak valid.");
            }
        }

        // Hapus session
        session()->forget(["snap_token", "payment_amount"]);

        $payment = Payment::find($paymentId);
        if ($payment && $payment->midtrans_status === "settlement") {
            $message = "Pembayaran berhasil!";
        } else {
            $message = "Pembayaran sedang diproses, harap tunggu konfirmasi.";
        }

        return redirect()
            ->route("filament.admin.resources.orders.view", [
                "record" => $orderNumber,
            ])
            ->with("success", $message);
    }

    /**
     * Webhook untuk notifikasi pembayaran dari Midtrans
     */
    public function notification(Request $request, MidtransService $midtrans)
    {
        $midtrans->handleNotification($request->getContent());
        return response()->json(["status" => "OK"]);
    }
}
