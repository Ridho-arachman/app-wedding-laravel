<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUlids;

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "order_number",
        "type",
        "amount",
        "payment_date",
        "method",
        "proof",
        "midtrans_order_id",
        "midtrans_status",
        "midtrans_response",
        "notes",
    ];

    protected $casts = [
        "payment_date" => "date",
        "midtrans_response" => "array",
    ];

    /**
     * Relasi ke Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, "order_number", "order_number");
    }
}
