<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_return', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->nullable()->constrained('expense_and_income')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->decimal('total_amount', 18, 3); // Jami summa
            $table->tinyInteger('currency');
            $table->decimal('rate', 15, 3)->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_return');
    }
};
