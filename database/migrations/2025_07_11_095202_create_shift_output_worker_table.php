<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_output_worker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_output_id')->constrained('shift_output')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('user')->cascadeOnDelete();
            $table->unsignedInteger('stage_count');
            $table->decimal('defect_amount', 10, 3)->default(0);
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('shift_output_worker', function (Blueprint $table) {
            $table->dropForeign(['shift_output_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('shift_output_worker');
    }
};
