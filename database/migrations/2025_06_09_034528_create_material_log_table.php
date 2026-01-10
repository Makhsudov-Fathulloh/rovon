<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_variation_id')->constrained('raw_material_variation')->cascadeOnDelete();
            
            $table->decimal('old_count', 15, 3);
            $table->decimal('added_count', 15, 3);
            $table->decimal('new_count', 15, 3);
            $table->foreignId('user_id')->constrained('user')->nullOnDelete();
            $table->string('action')->default('add_count'); // kelajak uchun
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('material_log', function (Blueprint $table) {
            $table->dropForeign(['raw_material_variation_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('material_log');
    }
};
