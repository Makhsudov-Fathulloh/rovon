<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pre_order_item', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pre_order_id')->index();
            $table->unsignedBigInteger('product_variation_id')->index();
            $table->string('code', 50);
            $table->decimal('count', 16, 3);
            $table->tinyInteger('unit'); // 1= dona, 2= kg
            $table->timestamps();

            $table->foreign('pre_order_id')->references('id')->on('pre_order')->cascadeOnDelete();
            $table->foreign('product_variation_id')->references('id')->on('product_variation')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pre_order_item', function (Blueprint $table) {
            $table->dropForeign(['pre_order_id']);
            $table->dropForeign(['product_variation_id']);
        });
        Schema::dropIfExists('pre_order');
    }
};
