<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('supplier')->onDelete('cascade');
            $table->foreignId('expense_id')->nullable()->constrained('expense_and_income')->nullOnDelete();
            $table->tinyInteger('type'); // 1-Yuk, 2-To'lov
            $table->decimal('amount', 16, 3);
            $table->tinyInteger('currency');
            $table->decimal('rate', 15, 3);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_item');
    }
};
