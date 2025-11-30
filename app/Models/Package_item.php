<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package_item extends Model
{
    /** @use HasFactory<\Database\Factories\PackageItemFactory> */
    use HasFactory;
    protected $fillable = [
        "name",
        "description",
        "price",
        "category",
        "image",
        "is_active",
    ];

    // Scope publik (untuk halaman /menu)
    public function scopePublic($query)
    {
        return $query->where("is_active", true);
    }

    public function getFormattedPriceAttribute(): string
    {
        return "Rp " . number_format($this->price, 0, ",", ".");
    }
}
