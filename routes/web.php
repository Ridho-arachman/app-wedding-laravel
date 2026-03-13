<?php

// use App\Http\Controllers\InvoiceController;

use App\Http\Controllers\MidtransController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::prefix("midtrans")
    ->name("midtrans.")
    ->group(function () {
        Route::get("/pay/{order}", [MidtransController::class, "pay"])->name(
            "pay",
        );
        Route::get("/finish", [MidtransController::class, "finish"])->name(
            "finish",
        );
        Route::post("/notification", [
            MidtransController::class,
            "notification",
        ])
            ->name("notification")
            ->withoutMiddleware([VerifyCsrfToken::class]);
    });

// Route::get("/invoice/{order}", [InvoiceController::class, "download"])->name(
//     "invoice.pdf",
// );
