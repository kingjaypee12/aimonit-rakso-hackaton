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
        Schema::create('game_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // game_started, question_shown, question_ended, game_completed
            $table->json('event_data')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['game_session_id', 'event_type']);
            $table->index('occurred_at');
            $table->index('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_events');
    }
};
