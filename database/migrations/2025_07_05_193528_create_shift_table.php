<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('section')->cascadeOnDelete();
            $table->string('title');
            $table->string('description')->nullable();
            $table->tinyInteger('status');
            $table->time('started_at');
            $table->time('ended_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shift', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });

        Schema::dropIfExists('shift');
    }
};
