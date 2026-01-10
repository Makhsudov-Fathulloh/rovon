<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('image')->nullable()->index();
            $table->tinyInteger('type');
            $table->string('slug');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');

            $table->foreign('parent_id')
                ->references('id')
                ->on('category')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('category', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('category');
    }
};
