<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_return_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_return_id')->constrained('product_return')->cascadeOnDelete();
            $table->foreignId('product_variation_id')->constrained('product_variation')->cascadeOnDelete();
            $table->decimal('count', 15, 3);
            $table->decimal('price', 15, 3);
            $table->decimal('total_price', 18, 3); // count * price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_return_item');
    }
};
