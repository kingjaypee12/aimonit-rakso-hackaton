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
        Schema::create('student_overall_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_games_played')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('total_correct_answers')->default(0);
            $table->integer('total_questions_answered')->default(0);
            $table->decimal('overall_accuracy', 5, 2)->default(0);
            $table->integer('first_place_finishes')->default(0);
            $table->integer('podium_finishes')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->decimal('average_answer_time', 8, 2)->default(0);
            $table->json('badges_earned')->nullable();
            $table->integer('level')->default(1);
            $table->timestamps();

            // Indexes for better performance
            $table->unique('student_id');
            $table->index('total_points');
            $table->index('overall_accuracy');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_overall_stats');
    }
};
