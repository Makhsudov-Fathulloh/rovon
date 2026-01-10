<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->nullable()->constrained('shift')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('user')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('shift_user', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('shift_user');
    }
};
