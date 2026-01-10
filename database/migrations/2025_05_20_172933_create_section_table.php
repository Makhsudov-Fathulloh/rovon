<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('previous_id')->nullable()->constrained('section')->nullOnDelete();
            $table->foreignId('organization_id')->constrained('organization')->onDelete('cascade');
            $table->string('title'); // 1-bo'lim", 2-bo'lim
            $table->text('description')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('section', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['previous_id']);
        });

        Schema::dropIfExists('section');
    }
};
