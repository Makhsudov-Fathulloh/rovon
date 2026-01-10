<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pre_order', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('count', 16, 3);
            $table->unsignedBigInteger('user_id')->nullable()->index(); // client
            $table->unsignedBigInteger('customer_id')->nullable()->index(); // buyurtmachi
            $table->tinyInteger('status')->default(1); // 1 = yangi, 2 = jarayonda, 3 = yakunlangan, 4 = bekor qilingan
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pre_order', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['customer_id']);
        });
        Schema::dropIfExists('pre_order');
    }
};
