<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('image')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('type')->nullable();
            $table->string('slug');
            $table->tinyInteger('status');
            $table->timestamps();

            // Foreign keys
            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouse')
                ->onDelete('cascade');

            // Foreign keys
            $table->foreign('image')
                ->references('id')
                ->on('file')
                ->nullOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');

            $table->foreign('category_id')
                ->references('id')
                ->on('category')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['image']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::dropIfExists('product');
    }
};
