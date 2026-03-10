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
        Schema::create("orders", function (Blueprint $table) {
            $table->string("order_number")->primary(); // WO-2025-0001
            $table->string("customer_name");
            $table->string("customer_phone");
            $table->text("customer_address")->nullable();
            $table->date("event_date");
            $table->string("package_code")->nullable(); // foreign key ke packages
            $table->integer("total_price");
            $table->integer("dp_amount");
            $table
                ->enum("status", [
                    "draft",
                    "dp_pending",
                    "dp_paid",
                    "installment",
                    "paid",
                    "completed",
                    "cancelled",
                ])
                ->default("draft");
            $table->text("notes")->nullable();
            $table->foreignId("created_by")->constrained("users");
            $table->timestamps();

            // Foreign key ke packages
            $table
                ->foreign("package_code")
                ->references("code")
                ->on("packages")
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("orders");
    }
};
