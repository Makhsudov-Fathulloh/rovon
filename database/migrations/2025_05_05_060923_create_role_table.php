<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::table('user', function (Blueprint $table) {
            $table->foreignId('role_id')    // role_id ustuni yaratadi
            ->nullable()                           // foydalanuvchi ro‘lga ega bo‘lmasligi mumkin
            ->constrained('role')            // role.id bilan bog‘laydi
            ->onDelete('set null');          // role o‘chirildida user.role_id null
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });

        Schema::dropIfExists('role');
    }
};
