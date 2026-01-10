<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 128)->nullable();
            $table->string('last_name', 128)->nullable();
            $table->string('username', 128);
            $table->string('address', 255)->nullable();
            $table->string('password_hash');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('photo')->nullable();
            $table->string('phone')->nullable();
            $table->bigInteger('telegram_chat_id')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->rememberToken();
            $table->string('token', 255);
            $table->string('auth_key', 32);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
