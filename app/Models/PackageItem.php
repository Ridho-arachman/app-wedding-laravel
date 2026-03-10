<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;

    protected $table = "package_items";
    protected $fillable = [
        "package_code",
        "item_code",
        "quantity",
        "unit",
        "sort_order",
    ];

    /**
     * Relasi ke Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class, "package_code", "code");
    }

    /**
     * Relasi ke Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, "item_code", "code");
    }
}
