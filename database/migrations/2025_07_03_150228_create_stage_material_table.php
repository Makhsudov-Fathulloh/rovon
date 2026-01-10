<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained('stage')->cascadeOnDelete();
            $table->foreignId('raw_material_variation_id')->constrained('raw_material_variation')->cascadeOnDelete();
            $table->decimal('count', 15, 3);
            $table->tinyInteger('unit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('stage_material', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->dropForeign(['raw_material_variation_id']);
        });

        Schema::dropIfExists('stage_material');
    }
};
