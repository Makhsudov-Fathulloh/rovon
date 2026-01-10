<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_variation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('raw_material_id')->index();
            $table->string('code', 50)->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('image')->nullable()->index();
            $table->decimal('count', 16, 3);
            $table->decimal('min_count', 10, 3);
            $table->tinyInteger('unit');

            // 💰 Narx va valyuta ustunlari
            $table->decimal('price', 16, 3);
            $table->tinyInteger('currency');                    // 1 = so‘m yoki 2 = $
            $table->decimal('rate', 15, 2)->default(1);         // 1$ = nechta so‘m
            $table->decimal('price_uzs', 15, 2)->default(0);    // so‘mda qiymat (price * rate)

            $table->decimal('total_price', 21, 3)->default(0);
            $table->string('type')->nullable();
            $table->string('slug');
            $table->tinyInteger('status');
            $table->timestamps();

            $table->foreign('raw_material_id')
                ->references('id')
                ->on('raw_material')
                ->onDelete('cascade');

            $table->foreign('image')
                ->references('id')
                ->on('file')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('raw_material_variation', function (Blueprint $table) {
            $table->dropForeign(['raw_material_id']);
            $table->dropForeign(['image']);
        });

        Schema::dropIfExists('raw_material_variation');
    }
};
