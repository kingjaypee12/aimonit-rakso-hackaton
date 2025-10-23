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
        Schema::create('game_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->json('overall_statistics'); // Total participants, avg score, completion rate
            $table->json('question_analysis'); // Per-question difficulty and comprehension
            $table->json('student_performance'); // Individual student comprehension levels
            $table->json('topic_comprehension'); // Understanding by topic/concept
            $table->json('podium_winners'); // Top 3 students
            $table->decimal('class_average_score', 5, 2);
            $table->integer('total_participants');
            $table->integer('questions_completed');
            $table->integer('total_answers_submitted');
            $table->decimal('average_completion_time', 8, 2)->nullable();
            $table->json('recommendations')->nullable(); // AI-generated insights
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_reports');
    }
};
