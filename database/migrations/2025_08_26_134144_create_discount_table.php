<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variation_id')->index();
            $table->unsignedBigInteger('order_item_id')->index();
            $table->decimal('sale_price', 15,3); // Sotish narxi
            $table->decimal('sold_price', 15,3);     // Sotilgan narx
            $table->decimal('amount', 15,3);    // Chegirma miqdori
            $table->decimal('count', 16, 3);
            $table->decimal('total_amount', 18,3); // Umumiy chegirma
            $table->timestamps();

            // Foreign keys
            $table->foreign('product_variation_id')
                ->references('id')
                ->on('product_variation')
                ->onDelete('cascade'); // agar product_variation o‘chsa, discount ham o‘chadi

            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_item')
                ->onDelete('cascade'); // agar order_item o‘chsa, discount ham o‘chadi
        });
    }

    public function down(): void
    {
        Schema::table('discount', function (Blueprint $table) {
            $table->dropForeign(['product_variation_id']);
            $table->dropForeign(['order_item_id']);
        });

        Schema::dropIfExists('discount');
    }
};
