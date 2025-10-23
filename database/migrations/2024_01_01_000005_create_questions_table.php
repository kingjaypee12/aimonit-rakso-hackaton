<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false'])->default('multiple_choice');
            $table->json('options'); // Array of answer options with colors
            $table->string('correct_answer'); // Index or value of correct answer
            $table->integer('points')->default(1000);
            $table->integer('time_limit_seconds')->default(20);
            $table->integer('order')->default(0);
            $table->text('explanation')->nullable();
            $table->string('image_url')->nullable(); // Optional question image
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};