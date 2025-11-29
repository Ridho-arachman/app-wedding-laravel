<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
          $table->id();
            $table->string('order_code')->unique();        // WO-2025-001
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();

            $table->integer('total_price');
            $table->integer('dp_amount');                  // 50% dari total
            $table->integer('remaining_amount');           // sisa

            $table->date('event_date');                    // tanggal acara
            $table->date('dp_paid_at')->nullable();        // kapan DP dibayar
            $table->date('full_paid_at')->nullable();      // kapan lunas

            // 🔑 Kolom Midtrans (siap integrasi)
            $table->string('midtrans_order_id')->nullable()->comment('order_id di Midtrans');
            $table->string('va_number')->nullable()->comment('Nomor VA dari Midtrans');
            $table->string('va_bank')->nullable()->comment('bca, mandiri, bni, dll');
            $table->string('payment_type')->nullable()->comment('bank_transfer, gopay, dll');
            $table->string('transaction_status')->default('pending')->comment('pending, settle, expire, cancel');
            $table->string('fraud_status')->nullable()->comment('accept, deny, challenge');

            $table->enum('status', [
                'draft',        // baru dibuat
                'dp_pending',   // VA sudah dibuat, menunggu bayar DP
                'dp_paid',      // DP sudah masuk
                'full_pending', // VA pelunasan dibuat
                'full_paid',    // lunas
                'completed',
                'cancelled'
            ])->default('draft');

            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained(); // admin yang buat
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_code');
            $table->index('customer_email');
            $table->index('event_date');
            $table->index('status');
            $table->index('midtrans_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
