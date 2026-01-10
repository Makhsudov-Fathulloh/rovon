<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shift_report', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('shift_id');
            $table->json('stage_product')->nullable();
            $table->decimal('defect_amount')->default(0);
            $table->tinyInteger('status')->default(1); // 1 = open, 2 = closed
            $table->timestamps();

            // Har smena 1 kunda faqat 1 marta kiradi
            $table->unique(['report_date', 'shift_id']);

            $table->foreign('shift_id')->references('id')->on('shift')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('section')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organization')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_report', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['shift_id']);
        });
        Schema::dropIfExists('shift_report');
    }
};
