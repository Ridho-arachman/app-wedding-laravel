<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        "order_code",
        "customer_name",
        "customer_email",
        "customer_phone",
        "notes",
        "total_price",
        "dp_amount",
        "remaining_amount",
        "event_date",
        "dp_paid_at",
        "full_paid_at",
        "midtrans_order_id",
        "va_number",
        "va_bank",
        "payment_type",
        "transaction_status",
        "fraud_status",
        "status",
        "package_id",
        "user_id",
    ];

    protected $casts = [
        "event_date" => "date",
        "dp_paid_at" => "date",
        "full_paid_at" => "date",
    ];

    // Relasi
    public function package(): BelongsTo
    {
        return $this->belongsTo(Packages::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Order_payment::class);
    }

    // 🔑 Auto-generate order_code & hitung DP saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_code) {
                $latest = self::orderBy("id", "desc")->first();
                $number = $latest
                    ? (int) substr($latest->order_code, -4) + 1
                    : 1;
                $order->order_code =
                    "WO-" .
                    now()->year .
                    "-" .
                    str_pad($number, 4, "0", STR_PAD_LEFT);
            }

            // Auto-hit DP 50% jika belum diisi
            if (!$order->dp_amount && $order->total_price) {
                $order->dp_amount = ceil($order->total_price * 0.5);
                $order->remaining_amount =
                    $order->total_price - $order->dp_amount;
            }
        });
    }

    // Accessor: sisa hari ke acara
    public function getDaysToEventAttribute(): int
    {
        return $this->event_date
            ? now()->diffInDays($this->event_date, false)
            : 0;
    }

    // Accessor: status display
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            "draft"
                => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded">Draft</span>',
            "dp_pending"
                => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Menunggu DP</span>',
            "dp_paid"
                => '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">DP Dibayar</span>',
            "full_pending"
                => '<span class="px-2 py-1 bg-orange-100 text-orange-800 rounded">Menunggu Lunas</span>',
            "full_paid"
                => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded">Lunas</span>',
            "completed"
                => '<span class="px-2 py-1 bg-purple-100 text-purple-800 rounded">Selesai</span>',
            "cancelled"
                => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded">Dibatalkan</span>',
            default => $this->status,
        };
    }

    // 🔑 Method: Generate VA via Midtrans (nanti diimplementasi di controller)
    public function generateVaForDp(): array
    {
        // Placeholder — nanti diisi dengan Midtrans SDK
        return [
            "order_id" => $this->order_code,
            "va_number" => "12345678901234",
            "va_bank" => "bca",
            "status" => "success",
        ];
    }

    // 🔑 Method: Cek status ke Midtrans
    public function checkPaymentStatus(): string
    {
        // Placeholder — nanti pakai Midtrans API
        return $this->transaction_status;
    }

    // Scope: order aktif (belum selesai/dibatalkan)
    public function scopeActive($query)
    {
        return $query->whereNotIn("status", ["completed", "cancelled"]);
    }

    // Scope: perlu reminder (H-7, H-3, dll)
    public function scopeNeedsReminder($query)
    {
        return $query
            ->where("status", "dp_paid")
            ->whereDate("event_date", ">=", now())
            ->where(function ($q) {
                $q->whereDate("event_date", now()->addDays(7))
                    ->orWhereDate("event_date", now()->addDays(3))
                    ->orWhereDate("event_date", now()->addDay());
            });
    }
}
