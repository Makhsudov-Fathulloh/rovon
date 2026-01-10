<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_output', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shift')->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained('stage')->cascadeOnDelete();
            $table->unsignedInteger('stage_count')->default(0);
            $table->decimal('defect_amount', 10, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('shift_output', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['stage_id']);
        });

        Schema::dropIfExists('shift_output');
    }
};
