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
        Schema::create("items", function (Blueprint $table) {
            $table->string("code")->primary(); // custom code: BRG-001
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("category")->nullable();
            $table->string("unit")->default("buah");
            $table->integer("stock")->default(0);
            $table->integer("min_stock")->default(0);
            $table->string("image")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("items");
    }
};
