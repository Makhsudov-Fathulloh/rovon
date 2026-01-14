<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_report', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');

            // $table->tinyInteger('currency');    // 1 = so‘m, 2 = $ ...
            // $table->decimal('total_order_amount', 18, 3)->default(0);
            // $table->decimal('total_amount_paid', 18, 3)->default(0);
            // $table->decimal('total_remaining_debt', 18, 3)->default(0);
            // $table->decimal('total_return_amount', 18, 3)->default(0);
            // $table->decimal('total_expense', 18, 3)->default(0);
            // $table->decimal('total_income', 18, 3)->default(0);
            // $table->decimal('total_debt_paid', 18, 3)->default(0);

            $table->json('total_order_amount')->default(json_encode([]));
            $table->json('total_amount_paid')->default(json_encode([]));
            $table->json('total_remaining_debt')->default(json_encode([]));
            $table->json('total_return_amount')->default(json_encode([]));
            $table->json('total_expense')->default(json_encode([]));
            $table->json('total_income')->default(json_encode([]));
            $table->json('total_debt_paid')->default(json_encode([]));

            $table->tinyInteger('status')->default(1); // 1 = open, 2 = closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_report');
    }
};
