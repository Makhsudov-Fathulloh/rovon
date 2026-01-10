<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            // Indexing
            $table->index('photo');
            // Foreign keys
            $table->foreign('photo')
                ->references('id')
                ->on('file')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign(['photo']);
        });
    }
};
