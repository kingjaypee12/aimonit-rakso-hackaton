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
        Schema::create('game_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained('game_participants')->onDelete('cascade');
            $table->string('answer_given');
            $table->boolean('is_correct')->default(false);
            $table->integer('points_earned')->default(0);
            $table->decimal('answer_time_seconds', 8, 2); // Time taken to answer
            $table->integer('answer_order')->nullable(); // Position in answering (1st, 2nd, 3rd...)
            $table->boolean('got_streak_bonus')->default(false);
            $table->timestamp('answered_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_answers');
    }
};
