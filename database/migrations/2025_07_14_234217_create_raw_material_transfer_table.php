<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_transfer', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->constrained('organization')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouse')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('section')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shift')->cascadeOnDelete();
            $table->unsignedBigInteger('shift_output_id')->nullable();
            $table->string('title');
            $table->unsignedBigInteger('sender_id'); // kim bergan (user_id)
            $table->unsignedBigInteger('receiver_id'); // kim olgan (user_id)
            $table->decimal('total_item_price', 21, 3)->default(0);
            $table->tinyInteger('status'); // draft, sent, received
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('user')->cascadeOnDelete();
            $table->foreign('receiver_id')->references('id')->on('user')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('raw_material_transfer', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
        });

        Schema::dropIfExists('raw_material_transfer');
    }
};
