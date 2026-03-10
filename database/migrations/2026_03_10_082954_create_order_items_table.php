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
        Schema::create("order_items", function (Blueprint $table) {
            $table->id();
            $table->string("order_number");
            $table->string("item_code");
            $table->integer("quantity");
            $table->string("unit")->nullable();
            $table->integer("price_per_unit")->nullable();
            $table->timestamps();

            $table
                ->foreign("order_number")
                ->references("order_number")
                ->on("orders")
                ->cascadeOnDelete();
            $table
                ->foreign("item_code")
                ->references("code")
                ->on("items")
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("order_items");
    }
};
