<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_and_income', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 16,3);
            $table->tinyInteger('currency'); // 1 = so‘m yoki 2 = $
            $table->smallInteger('type'); // 'debt', 'income', 'expense'.
            $table->smallInteger('type_payment'); // 'cash', 'transfer', 'bank'.
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('expense_and_income', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('expense_and_income');
    }
};
