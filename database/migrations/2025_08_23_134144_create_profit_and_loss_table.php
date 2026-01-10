<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_and_loss', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variation_id')->index();
            $table->unsignedBigInteger('order_item_id')->index();
            $table->decimal('original_price', 15,3); // Asl narxi
            $table->decimal('sold_price', 15,3);     // Sotilgan narx
            $table->decimal('profit_amount', 15,3);    // Foyda miqdori
            $table->decimal('loss_amount', 15, 3);    // Ziyon miqdori
            $table->decimal('count', 16, 3);
            $table->tinyInteger('type'); // 'Profit', 'Loss'
            $table->decimal('total_amount', 21,3); // Umumiy foyda va zarar
            $table->timestamps();

            // Foreign keys
            $table->foreign('product_variation_id')
                ->references('id')
                ->on('product_variation')
                ->onDelete('cascade'); // agar product_variation o‘chsa, profit_and_loss ham o‘chadi

            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_item')
                ->onDelete('cascade'); // agar order_item o‘chsa, profit_and_loss ham o‘chadi
        });
    }

    public function down(): void
    {
        Schema::table('profit_and_loss', function (Blueprint $table) {
            $table->dropForeign(['product_variation_id']);
            $table->dropForeign(['order_item_id']);
        });

        Schema::dropIfExists('profit_and_loss');
    }
};
