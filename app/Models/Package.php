<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";
    protected $primaryKey = "code";

    protected $fillable = [
        "code",
        "name",
        "description",
        "price",
        "image",
        "is_active",
    ];

    protected $casts = [
        "is_active" => "boolean",
    ];

    /**
     * Relasi many-to-many dengan Item melalui package_items
     */
    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            "package_items",
            "package_code",
            "item_code",
        )
            ->withPivot("quantity", "unit", "sort_order")
            ->withTimestamps();
    }

    /**
     * Relasi ke Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class, "package_code", "code");
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class, "package_code", "code");
    }
}
