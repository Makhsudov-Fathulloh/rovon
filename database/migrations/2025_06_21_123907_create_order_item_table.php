<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_variation_id')->nullable()->index();
            $table->decimal('quantity', 16, 3);
            $table->decimal('price', 15, 3)->default(0);
            $table->decimal('total_price', 18, 3)->default(0);
            $table->decimal('price_base', 15, 2)->default(0);
            $table->decimal('total_price_base', 18, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')
                ->references('id')
                ->on('order')
                ->onDelete('cascade');

            $table->foreign('product_variation_id')
                ->references('id')
                ->on('product_variation')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_item', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_variation_id']);
        });

        Schema::dropIfExists('order_item');
    }
};
