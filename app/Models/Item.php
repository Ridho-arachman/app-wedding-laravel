<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;
    protected $fillable = [
        "name",
        "code",
        "description",
        "stock",
        "condition",
        "category",
        "buy_price",
        "acquired_at",
    ];

    protected $casts = [
        "acquired_at" => "date",
        "buy_price" => "decimal:2",
    ];

    // Accessor kondisi badge
    public function getConditionBadgeAttribute(): string
    {
        return match ($this->condition) {
            "baru"
                => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded">Baru</span>',
            "bekas"
                => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Bekas</span>',
            "perlu_perbaikan"
                => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded">Perlu Perbaikan</span>',
            default => $this->condition,
        };
    }
}
