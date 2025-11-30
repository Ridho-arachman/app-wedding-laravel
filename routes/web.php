<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/invoice/{order}", [InvoiceController::class, "download"])->name(
    "invoice.pdf",
);
