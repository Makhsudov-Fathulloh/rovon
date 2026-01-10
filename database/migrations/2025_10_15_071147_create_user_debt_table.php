<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_debt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('user')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('order')->cascadeOnDelete();
            $table->decimal('amount', 16, 3);
            $table->string('currency', 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('user_debt', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['order_id']);
        });

        Schema::dropIfExists('user_debt');
    }
};
