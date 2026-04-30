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
 Schema::table('users', function (Blueprint $table) {
        $table->string('phone', 15)->nullable();
        $table->string('city', 100);
        $table->fullText(['name', 'email', 'city'], 'users_fulltext_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
       Schema::dropIfExists('users');
        });
    }
};
