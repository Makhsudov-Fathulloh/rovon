<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('seller_id')->index();
            $table->tinyInteger('status')->default(1);
            $table->decimal('total_price', 21, 3)->default(0);
            $table->decimal('total_price_base', 21, 2)->default(0);
            $table->decimal('cash_paid', 15, 2)->default(0); // naqd to'lov
            $table->decimal('card_paid', 15, 2)->default(0); // karta orqali to'lov
            $table->decimal('transfer_paid', 15, 2)->default(0); // o'tkazma orqali to'lov
            $table->decimal('bank_paid', 15, 2)->default(0); // bank orqali to'lov
            $table->decimal('total_amount_paid', 15, 2)->default(0); // jami to'langan_summa
            $table->decimal('remaining_debt', 18, 3)->default(0); // qolgan_qarz
            $table->tinyInteger('currency');    // 1 = so‘m, 2 = $ ...
            $table->decimal('exchange_rate', 15, 2)->default(1); // Valyuta kursi (UZS ga nisbatan)
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');

            $table->foreign('seller_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['seller_id']);
        });

        Schema::dropIfExists('order');
    }
};
