<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";
    protected $primaryKey = "order_number";

    protected $fillable = [
        "order_number",
        "customer_name",
        "customer_phone",
        "customer_address",
        "event_date",
        "package_code",
        "total_price",
        "dp_amount",
        "additional_charge",
        "charge_description",
        "status",
        "notes",
        "created_by",
    ];

    protected $casts = [
        "event_date" => "date",
    ];

    /**
     * Relasi ke Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class, "package_code", "code");
    }

    /**
     * Relasi ke OrderItem
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, "order_number", "order_number");
    }

    /**
     * Relasi ke Payment
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, "order_number", "order_number");
    }

    /**
     * Relasi ke User (admin pembuat)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
