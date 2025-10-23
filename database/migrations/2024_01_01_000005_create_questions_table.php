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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('concept'); // The specific concept being tested
            $table->text('question'); // The question text
            $table->json('options'); // {"A": "Option A", "B": "Option B", "C": "Option C", "D": "Option D"}
            $table->string('correct'); // The correct answer key (A, B, C, or D)
            $table->text('explanation'); // Explanation for the correct answer
            $table->integer('points')->default(1000);
            $table->integer('time_limit_seconds')->default(20);
            $table->integer('order')->default(0);
            $table->string('image_url')->nullable(); // Optional question image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};