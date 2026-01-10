<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organization')->cascadeOnDelete();
            $table->string('title');
            $table->string('description')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });

        Schema::dropIfExists('warehouse');
    }
};
