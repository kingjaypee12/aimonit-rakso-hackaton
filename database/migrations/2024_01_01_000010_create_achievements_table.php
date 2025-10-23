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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained('game_participants')->onDelete('cascade');
            $table->string('achievement_type'); // fastest_answer, perfect_score, comeback_king, streak_master
            $table->string('achievement_name');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->integer('bonus_points')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('earned_at');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['game_session_id', 'participant_id']);
            $table->index('achievement_type');
            $table->index('earned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};