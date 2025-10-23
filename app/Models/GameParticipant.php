<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'student_id',
        'nickname',
        'total_score',
        'correct_answers',
        'incorrect_answers',
        'current_streak',
        'longest_streak',
        'rank',
        'average_answer_time',
        'is_active',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'total_score' => 'integer',
        'correct_answers' => 'integer',
        'incorrect_answers' => 'integer',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'rank' => 'integer',
        'average_answer_time' => 'decimal:2',
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    /**
     * Get the game session that this participant belongs to.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the student (user) for this participant.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get all answers submitted by this participant.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(GameAnswer::class, 'participant_id');
    }

    /**
     * Calculate accuracy percentage.
     */
    public function getAccuracyAttribute(): float
    {
        $totalAnswers = $this->correct_answers + $this->incorrect_answers;
        
        if ($totalAnswers === 0) {
            return 0;
        }

        return round(($this->correct_answers / $totalAnswers) * 100, 2);
    }

    /**
     * Get display name (nickname or student name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?: $this->student->name;
    }

    /**
     * Update participant's score and statistics.
     */
    public function updateScore(int $points, bool $isCorrect, float $answerTime): void
    {
        $this->total_score += $points;
        
        if ($isCorrect) {
            $this->correct_answers++;
            $this->current_streak++;
            $this->longest_streak = max($this->longest_streak, $this->current_streak);
        } else {
            $this->incorrect_answers++;
            $this->current_streak = 0;
        }

        // Update average answer time
        $totalAnswers = $this->correct_answers + $this->incorrect_answers;
        $this->average_answer_time = (($this->average_answer_time * ($totalAnswers - 1)) + $answerTime) / $totalAnswers;

        $this->save();
    }

    /**
     * Mark participant as inactive (left the game).
     */
    public function markAsLeft(): void
    {
        $this->is_active = false;
        $this->left_at = now();
        $this->save();
    }

    /**
     * Scope for active participants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for participants ordered by rank.
     */
    public function scopeByRank($query)
    {
        return $query->orderBy('rank');
    }

    /**
     * Scope for participants ordered by score (descending).
     */
    public function scopeByScore($query)
    {
        return $query->orderByDesc('total_score');
    }
}