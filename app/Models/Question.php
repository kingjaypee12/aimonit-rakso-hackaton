<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'points',
        'time_limit_seconds',
        'order',
        'explanation',
        'image_url',
        'metadata',
    ];

    protected $casts = [
        'options' => 'array',
        'metadata' => 'array',
        'points' => 'integer',
        'time_limit_seconds' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the game session that owns the question.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Scope a query to order questions by their order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope a query to only include multiple choice questions.
     */
    public function scopeMultipleChoice($query)
    {
        return $query->where('question_type', 'multiple_choice');
    }

    /**
     * Scope a query to only include true/false questions.
     */
    public function scopeTrueFalse($query)
    {
        return $query->where('question_type', 'true_false');
    }

    /**
     * Check if the question is multiple choice.
     */
    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'multiple_choice';
    }

    /**
     * Check if the question is true/false.
     */
    public function isTrueFalse(): bool
    {
        return $this->question_type === 'true_false';
    }

    /**
     * Check if a given answer is correct.
     */
    public function isCorrectAnswer(string $answer): bool
    {
        return $this->correct_answer === $answer;
    }

    /**
     * Get the formatted options for display.
     */
    public function getFormattedOptions(): array
    {
        if ($this->isTrueFalse()) {
            return [
                ['text' => 'True', 'color' => 'green'],
                ['text' => 'False', 'color' => 'red'],
            ];
        }

        return $this->options ?? [];
    }

    /**
     * Calculate points based on answer speed (optional feature).
     */
    public function calculatePoints(int $timeToAnswer): int
    {
        $basePoints = $this->points;
        $timeLimit = $this->time_limit_seconds;
        
        // Award full points if answered quickly, reduce points as time increases
        $speedMultiplier = max(0.1, ($timeLimit - $timeToAnswer) / $timeLimit);
        
        return (int) round($basePoints * $speedMultiplier);
    }
}