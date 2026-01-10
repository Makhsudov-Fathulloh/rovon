<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('file', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->string('extension');
            $table->string('mime_type');
            $table->integer('size');
            $table->integer('date_create');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('file', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('file');
    }
};
