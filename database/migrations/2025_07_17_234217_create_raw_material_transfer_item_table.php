<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('raw_material_transfer_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_transfer_id')->constrained('raw_material_transfer')->cascadeOnDelete();
            $table->foreignId('raw_material_variation_id')->constrained('raw_material_variation')->cascadeOnDelete();
            $table->decimal('count', 16, 3);
            $table->tinyInteger('unit');
            $table->decimal('price', 16, 3);
            $table->decimal('total_price', 21, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('raw_material_transfer_item', function (Blueprint $table) {
            $table->dropForeign(['raw_material_transfer_id']);
            $table->dropForeign(['raw_material_variation_id']);
        });

        Schema::dropIfExists('raw_material_transfer_item');
    }
};
