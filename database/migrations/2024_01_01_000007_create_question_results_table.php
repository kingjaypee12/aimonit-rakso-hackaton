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
        Schema::create('question_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('total_answers')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('incorrect_answers')->default(0);
            $table->json('answer_distribution'); // Count per option
            $table->decimal('average_answer_time', 8, 2)->default(0);
            $table->string('fastest_answer_by')->nullable(); // Student name
            $table->decimal('fastest_answer_time', 8, 2)->nullable();
            $table->decimal('difficulty_rating', 3, 2)->nullable(); // Based on correct %
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_results');
    }
};