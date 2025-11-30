<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Packages extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "description",
        "price",
        "image",
        "is_active",
    ];

    public function getImageUrlAttribute()
    {
        return asset("storage/" . $this->image);
    }
    // Relasi ke Menu & Item via package_items
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, "package_items")
            ->withPivot("quantity")
            ->withTimestamps();
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, "package_items")
            ->withPivot("quantity")
            ->withTimestamps();
    }

    // Accessor: harga dengan format
    public function getFormattedPriceAttribute(): string
    {
        return "Rp " . number_format($this->price, 0, ",", ".");
    }

    // Scope aktif
    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }
}
