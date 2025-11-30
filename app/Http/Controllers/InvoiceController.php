<?php

namespace App\Http\Controllers;

use App\Models\Order;
use function Spatie\LaravelPdf\Support\pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        return pdf()
            ->view("invoices.order", compact("order"))
            ->name("Invoice-{$order->order_code}.pdf");
        // return Pdf::view("invoices.order", compact("order"))
        //     ->format("a4")
        //     ->inline("Invoice-{$order->order_code}.pdf");
    }
}
