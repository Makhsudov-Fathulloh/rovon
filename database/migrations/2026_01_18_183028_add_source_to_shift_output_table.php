<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shift_output', function (Blueprint $table) {
            // Agar bu null bo'lsa, stage modelidagi standart pre_stage_id ishlatiladi
            $table->foreignId('source_stage_id')->after('stage_id')->nullable()->constrained('stage')->nullOnDelete();
            // Agar manba boshqa sectionda bo'lsa, uni ham saqlashimiz mumkin
            $table->foreignId('source_section_id')->after('source_stage_id')->nullable()->constrained('section')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shift_output', function (Blueprint $table) {
            $table->dropForeign(['source_stage_id']);
            $table->dropForeign(['source_section_id']);

            $table->dropColumn(['source_stage_id', 'source_section_id']);
        });
    }
};
