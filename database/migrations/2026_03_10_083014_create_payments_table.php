<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("payments", function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->string("order_number");
            $table->enum("type", ["dp", "installment", "final"]);
            $table->integer("amount");
            $table->date("payment_date")->nullable(); // tambahkan nullable()
            $table->enum("method", ["cash", "transfer", "midtrans"]);
            $table->string("proof")->nullable();
            $table->string("midtrans_order_id")->nullable();
            $table->string("midtrans_status")->nullable();
            $table->json("midtrans_response")->nullable();
            $table->text("notes")->nullable();
            $table->timestamps();

            $table
                ->foreign("order_number")
                ->references("order_number")
                ->on("orders")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("payments");
    }
};
