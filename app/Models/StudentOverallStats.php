<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentOverallStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_games_played',
        'total_points',
        'total_correct_answers',
        'total_questions_answered',
        'overall_accuracy',
        'first_place_finishes',
        'podium_finishes',
        'longest_streak',
        'average_answer_time',
        'badges_earned',
        'level',
    ];

    protected $casts = [
        'total_games_played' => 'integer',
        'total_points' => 'integer',
        'total_correct_answers' => 'integer',
        'total_questions_answered' => 'integer',
        'overall_accuracy' => 'decimal:2',
        'first_place_finishes' => 'integer',
        'podium_finishes' => 'integer',
        'longest_streak' => 'integer',
        'average_answer_time' => 'decimal:2',
        'badges_earned' => 'array',
        'level' => 'integer',
    ];

    // Level thresholds
    public const LEVEL_THRESHOLDS = [
        1 => 0,
        2 => 100,
        3 => 250,
        4 => 500,
        5 => 1000,
        6 => 2000,
        7 => 3500,
        8 => 5500,
        9 => 8000,
        10 => 12000,
    ];

    /**
     * Get the student that these stats belong to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Update stats after a game session.
     */
    public function updateAfterGame(GameParticipant $participant): void
    {
        $this->increment('total_games_played');
        $this->increment('total_points', $participant->total_score);
        $this->increment('total_correct_answers', $participant->correct_answers);
        $this->increment('total_questions_answered', $participant->correct_answers + $participant->incorrect_answers);

        // Update longest streak if this game had a better one
        if ($participant->longest_streak > $this->longest_streak) {
            $this->longest_streak = $participant->longest_streak;
        }

        // Update podium finishes
        if ($participant->rank <= 3) {
            $this->increment('podium_finishes');
            
            if ($participant->rank === 1) {
                $this->increment('first_place_finishes');
            }
        }

        // Recalculate overall accuracy
        $this->overall_accuracy = $this->total_questions_answered > 0 
            ? ($this->total_correct_answers / $this->total_questions_answered) * 100 
            : 0;

        // Recalculate average answer time
        $this->recalculateAverageAnswerTime();

        // Update level based on total points
        $this->updateLevel();

        $this->save();
    }

    /**
     * Recalculate average answer time across all games.
     */
    protected function recalculateAverageAnswerTime(): void
    {
        $participants = GameParticipant::where('student_id', $this->student_id)->get();
        
        if ($participants->count() > 0) {
            $totalTime = $participants->sum('average_answer_time');
            $this->average_answer_time = $totalTime / $participants->count();
        }
    }

    /**
     * Update the student's level based on total points.
     */
    protected function updateLevel(): void
    {
        $newLevel = 1;
        
        foreach (self::LEVEL_THRESHOLDS as $level => $threshold) {
            if ($this->total_points >= $threshold) {
                $newLevel = $level;
            } else {
                break;
            }
        }

        $this->level = $newLevel;
    }

    /**
     * Add a badge to the student.
     */
    public function addBadge(string $badge): void
    {
        $badges = $this->badges_earned ?? [];
        
        if (!in_array($badge, $badges)) {
            $badges[] = $badge;
            $this->badges_earned = $badges;
            $this->save();
        }
    }

    /**
     * Check if student has a specific badge.
     */
    public function hasBadge(string $badge): bool
    {
        return in_array($badge, $this->badges_earned ?? []);
    }

    /**
     * Get the points needed for the next level.
     */
    public function getPointsToNextLevelAttribute(): int
    {
        $nextLevel = $this->level + 1;
        
        if (isset(self::LEVEL_THRESHOLDS[$nextLevel])) {
            return self::LEVEL_THRESHOLDS[$nextLevel] - $this->total_points;
        }

        return 0; // Max level reached
    }

    /**
     * Get the progress percentage to next level.
     */
    public function getLevelProgressAttribute(): float
    {
        $currentLevelThreshold = self::LEVEL_THRESHOLDS[$this->level] ?? 0;
        $nextLevelThreshold = self::LEVEL_THRESHOLDS[$this->level + 1] ?? $this->total_points;

        if ($nextLevelThreshold === $currentLevelThreshold) {
            return 100; // Max level
        }

        $progress = (($this->total_points - $currentLevelThreshold) / ($nextLevelThreshold - $currentLevelThreshold)) * 100;
        
        return min(100, max(0, $progress));
    }

    /**
     * Get students ordered by total points.
     */
    public function scopeOrderByPoints($query, string $direction = 'desc')
    {
        return $query->orderBy('total_points', $direction);
    }

    /**
     * Get students ordered by accuracy.
     */
    public function scopeOrderByAccuracy($query, string $direction = 'desc')
    {
        return $query->orderBy('overall_accuracy', $direction);
    }

    /**
     * Get students by level.
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Get top performers (students with high accuracy and points).
     */
    public function scopeTopPerformers($query, int $limit = 10)
    {
        return $query->where('overall_accuracy', '>=', 80)
                    ->where('total_games_played', '>=', 5)
                    ->orderBy('total_points', 'desc')
                    ->limit($limit);
    }

    /**
     * Get students who need improvement.
     */
    public function scopeNeedImprovement($query, int $limit = 10)
    {
        return $query->where('overall_accuracy', '<', 60)
                    ->where('total_games_played', '>=', 3)
                    ->orderBy('overall_accuracy', 'asc')
                    ->limit($limit);
    }

    /**
     * Get the student's rank based on total points.
     */
    public function getRankAttribute(): int
    {
        return self::where('total_points', '>', $this->total_points)->count() + 1;
    }

    /**
     * Get performance summary.
     */
    public function getPerformanceSummary(): array
    {
        return [
            'level' => $this->level,
            'total_points' => $this->total_points,
            'games_played' => $this->total_games_played,
            'accuracy' => round($this->overall_accuracy, 1),
            'first_places' => $this->first_place_finishes,
            'podium_finishes' => $this->podium_finishes,
            'longest_streak' => $this->longest_streak,
            'average_time' => round($this->average_answer_time, 2),
            'badges_count' => count($this->badges_earned ?? []),
            'rank' => $this->rank,
            'level_progress' => round($this->level_progress, 1),
            'points_to_next_level' => $this->points_to_next_level,
        ];
    }
}