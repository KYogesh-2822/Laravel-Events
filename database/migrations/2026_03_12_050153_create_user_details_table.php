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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()                        // one-to-one
                  ->constrained()
                  ->cascadeOnDelete();
 
            $table->string('profession', 100);
            $table->text('bio')->nullable();
            $table->tinyInteger('age')->unsigned();
            $table->tinyInteger('experience')->unsigned()->comment('years');
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('India');
            $table->timestamps();
 
            // ① FULL-TEXT INDEX on user_details columns
            $table->fullText(['profession', 'bio', 'state'], 'user_details_fulltext_idx');
 
            // Regular B-Tree index for JOIN performance (user_id already indexed via unique)
            $table->index(['user_id', 'age']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
