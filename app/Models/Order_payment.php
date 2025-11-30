<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_payment extends Model
{
    /** @use HasFactory<\Database\Factories\OrderPaymentFactory> */
    use HasFactory;

    protected $fillable = ["order_id", "type", "amount", "proof", "paid_at"];

    protected $casts = [
        "paid_at" => "date",
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Accessor: tipe pembayaran
    public function getTypeLabelAttribute(): string
    {
        return $this->type === "dp" ? "DP" : "Pelunasan";
    }
}
