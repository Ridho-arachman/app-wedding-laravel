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
        Schema::create("package_items", function (Blueprint $table) {
            $table->id(); // tetap auto-increment untuk pivot
            $table->string("package_code");
            $table->string("item_code");
            $table->integer("quantity")->default(1);
            $table->string("unit")->nullable();
            $table->integer("sort_order")->default(0);
            $table->timestamps();

            // Foreign keys
            $table
                ->foreign("package_code")
                ->references("code")
                ->on("packages")
                ->cascadeOnDelete();
            $table
                ->foreign("item_code")
                ->references("code")
                ->on("items")
                ->cascadeOnDelete();

            // Unique constraint
            $table->unique(["package_code", "item_code"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("package_items");
    }
};
