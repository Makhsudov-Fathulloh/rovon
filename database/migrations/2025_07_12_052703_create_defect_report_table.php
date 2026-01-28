<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defect_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organization')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('section')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shift')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('stage_id')->constrained('stage')->onDelete('cascade');
            $table->integer('stage_count')->default(0);
            $table->float('defect_amount')->default(0);
            $table->float('total_defect_amount')->default(0);
            $table->tinyInteger('defect_type');
            $table->float('defect_percent')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('defect_report', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['stage_id']);
        });

        Schema::dropIfExists('defect_report');
    }
};
