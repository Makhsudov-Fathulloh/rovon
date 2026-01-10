<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('section')->cascadeOnDelete();
            $table->foreignId('pre_stage_id')->nullable()->constrained('stage')->nullOnDelete();
            $table->string('title'); // 1-bo'lim", 2-bo'lim
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('stage', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropForeign(['pre_stage_id']);
        });

        Schema::dropIfExists('stage');
    }
};
