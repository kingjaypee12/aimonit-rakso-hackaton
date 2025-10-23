<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'question_id',
        'participant_id',
        'answer_given',
        'is_correct',
        'points_earned',
        'answer_time_seconds',
        'answer_order',
        'got_streak_bonus',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
        'answer_time_seconds' => 'decimal:2',
        'answer_order' => 'integer',
        'got_streak_bonus' => 'boolean',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the game session that this answer belongs to.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the question that this answer is for.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the participant who submitted this answer.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(GameParticipant::class, 'participant_id');
    }

    /**
     * Check if this answer was submitted within the time limit.
     */
    public function isWithinTimeLimit(): bool
    {
        $timeLimit = $this->question->time_limit_seconds;
        
        if (!$timeLimit) {
            return true;
        }

        return $this->answer_time_seconds <= $timeLimit;
    }

    /**
     * Get the speed bonus multiplier based on answer time.
     */
    public function getSpeedBonusMultiplier(): float
    {
        $timeLimit = $this->question->time_limit_seconds;
        
        if (!$timeLimit || !$this->is_correct) {
            return 1.0;
        }

        // Faster answers get higher multipliers (up to 2x for very fast answers)
        $timeRatio = $this->answer_time_seconds / $timeLimit;
        
        if ($timeRatio <= 0.25) {
            return 2.0; // Very fast (within 25% of time limit)
        } elseif ($timeRatio <= 0.5) {
            return 1.5; // Fast (within 50% of time limit)
        } elseif ($timeRatio <= 0.75) {
            return 1.25; // Moderate (within 75% of time limit)
        }
        
        return 1.0; // Normal speed
    }

    /**
     * Calculate the final points including bonuses.
     */
    public function calculateFinalPoints(): int
    {
        if (!$this->is_correct) {
            return 0;
        }

        $basePoints = $this->question->points;
        $speedMultiplier = $this->getSpeedBonusMultiplier();
        $streakBonus = $this->got_streak_bonus ? 100 : 0;

        return (int) round(($basePoints * $speedMultiplier) + $streakBonus);
    }

    /**
     * Scope for correct answers.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope for incorrect answers.
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Scope for answers ordered by submission time.
     */
    public function scopeByAnswerTime($query)
    {
        return $query->orderBy('answer_time_seconds');
    }

    /**
     * Scope for answers ordered by submission order.
     */
    public function scopeByAnswerOrder($query)
    {
        return $query->orderBy('answer_order');
    }

    /**
     * Scope for answers with streak bonus.
     */
    public function scopeWithStreakBonus($query)
    {
        return $query->where('got_streak_bonus', true);
    }
}