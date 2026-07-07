<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('city', 100)->nullable();
            $table->boolean('is_admin')->default(false);

            if (DB::getDriverName() === 'mysql') {
                $table->fullText(['name', 'email', 'city'], 'users_fulltext_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'city', 'is_admin']);
        });
    }
};
