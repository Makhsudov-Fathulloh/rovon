<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_stage_balance', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->constrained('organization')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('section')->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained('stage')->cascadeOnDelete();
            $table->decimal('in_qty', 18, 3)->default(0);
            $table->decimal('out_qty', 18, 3)->default(0);
            $table->decimal('balance', 18, 3)->default(0);
            $table->timestamps();

            $table->unique(['section_id', 'stage_id']);
        });
    }

    public function down(): void
    {
        Schema::table('section_stage_balance', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['stage_id']);
        });

        Schema::dropIfExists('section_stage_balance');
    }
};
