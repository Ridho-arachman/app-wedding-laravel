<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = "order_items";
    protected $fillable = [
        "order_number",
        "item_code",
        "quantity",
        "unit",
        "price_per_unit",
    ];

    /**
     * Relasi ke Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, "order_number", "order_number");
    }

    /**
     * Relasi ke Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, "item_code", "code");
    }
}
