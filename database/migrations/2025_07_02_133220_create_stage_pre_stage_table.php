<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_pre_stage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained('stage')->cascadeOnDelete();
            $table->foreignId('pre_stage_id')->constrained('stage')->cascadeOnDelete();
            // $table->unique(['stage_id', 'pre_stage_id']);
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
