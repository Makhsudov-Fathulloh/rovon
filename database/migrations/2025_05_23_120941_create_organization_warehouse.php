<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organization')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouse')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['organization_id', 'warehouse_id']); // duplicate oldini olish
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_warehouse');
    }
};
