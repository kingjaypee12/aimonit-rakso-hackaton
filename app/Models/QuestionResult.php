<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'question_id',
        'total_answers',
        'correct_answers',
        'incorrect_answers',
        'answer_distribution',
        'average_answer_time',
        'fastest_answer_by',
        'fastest_answer_time',
        'difficulty_rating',
    ];

    protected $casts = [
        'total_answers' => 'integer',
        'correct_answers' => 'integer',
        'incorrect_answers' => 'integer',
        'answer_distribution' => 'array',
        'average_answer_time' => 'decimal:2',
        'fastest_answer_time' => 'decimal:2',
        'difficulty_rating' => 'decimal:2',
    ];

    /**
     * Get the game session that this result belongs to.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the question that this result is for.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Calculate the accuracy percentage for this question.
     */
    public function getAccuracyPercentageAttribute(): float
    {
        if ($this->total_answers === 0) {
            return 0;
        }

        return round(($this->correct_answers / $this->total_answers) * 100, 2);
    }

    /**
     * Get the difficulty level based on accuracy percentage.
     */
    public function getDifficultyLevelAttribute(): string
    {
        $accuracy = $this->accuracy_percentage;

        if ($accuracy >= 80) {
            return 'Easy';
        } elseif ($accuracy >= 60) {
            return 'Medium';
        } elseif ($accuracy >= 40) {
            return 'Hard';
        } else {
            return 'Very Hard';
        }
    }

    /**
     * Get the most popular answer option.
     */
    public function getMostPopularAnswerAttribute(): ?string
    {
        if (empty($this->answer_distribution)) {
            return null;
        }

        return array_keys($this->answer_distribution, max($this->answer_distribution))[0] ?? null;
    }

    /**
     * Get the least popular answer option.
     */
    public function getLeastPopularAnswerAttribute(): ?string
    {
        if (empty($this->answer_distribution)) {
            return null;
        }

        $nonZeroAnswers = array_filter($this->answer_distribution, fn ($count) => $count > 0);

        if (empty($nonZeroAnswers)) {
            return null;
        }

        return array_keys($nonZeroAnswers, min($nonZeroAnswers))[0] ?? null;
    }

    /**
     * Update the question result with a new answer.
     */
    public function updateWithAnswer(string $answer, bool $isCorrect, float $answerTime, string $studentName): void
    {
        $this->total_answers++;

        if ($isCorrect) {
            $this->correct_answers++;
        } else {
            $this->incorrect_answers++;
        }

        // Update answer distribution
        $distribution = $this->answer_distribution ?? [];
        $distribution[$answer] = ($distribution[$answer] ?? 0) + 1;
        $this->answer_distribution = $distribution;

        // Update average answer time
        $this->average_answer_time = (($this->average_answer_time * ($this->total_answers - 1)) + $answerTime) / $this->total_answers;

        // Update fastest answer if this is faster
        if ($this->fastest_answer_time === null || $answerTime < $this->fastest_answer_time) {
            $this->fastest_answer_time = $answerTime;
            $this->fastest_answer_by = $studentName;
        }

        // Calculate difficulty rating based on accuracy
        $this->difficulty_rating = $this->calculateDifficultyRating();

        $this->save();
    }

    /**
     * Calculate difficulty rating based on various factors.
     */
    protected function calculateDifficultyRating(): float
    {
        if ($this->total_answers === 0) {
            return 0;
        }

        $accuracy = $this->accuracy_percentage;
        $avgTime = $this->average_answer_time;
        $timeLimit = $this->question->time_limit_seconds ?? 30;

        // Base difficulty from accuracy (inverted - lower accuracy = higher difficulty)
        $accuracyDifficulty = (100 - $accuracy) / 100 * 5;

        // Time difficulty (if average time is close to limit, it's harder)
        $timeDifficulty = ($avgTime / $timeLimit) * 2;

        // Combine factors (weighted average)
        $difficulty = ($accuracyDifficulty * 0.7) + ($timeDifficulty * 0.3);

        // Ensure rating is between 0 and 5
        return round(min(5, max(0, $difficulty)), 2);
    }

    /**
     * Get comprehension insights for this question.
     */
    public function getComprehensionInsights(): array
    {
        $insights = [];

        $accuracy = $this->accuracy_percentage;

        if ($accuracy < 50) {
            $insights[] = 'This question was challenging for most students. Consider reviewing this topic.';
        } elseif ($accuracy > 90) {
            $insights[] = 'Students performed excellently on this question. The concept is well understood.';
        }

        if ($this->average_answer_time > ($this->question->time_limit_seconds ?? 30) * 0.8) {
            $insights[] = 'Students took a long time to answer. The question might be complex or unclear.';
        }

        $mostPopular = $this->most_popular_answer;
        $correctAnswer = $this->question->correct_answer;

        if ($mostPopular && $mostPopular !== $correctAnswer) {
            $insights[] = "Many students chose '{$mostPopular}' instead of the correct answer. This might indicate a common misconception.";
        }

        return $insights;
    }

    /**
     * Scope for questions with low accuracy.
     */
    public function scopeLowAccuracy($query, float $threshold = 60)
    {
        return $query->whereRaw('(correct_answers / total_answers * 100) < ?', [$threshold]);
    }

    /**
     * Scope for questions with high accuracy.
     */
    public function scopeHighAccuracy($query, float $threshold = 80)
    {
        return $query->whereRaw('(correct_answers / total_answers * 100) >= ?', [$threshold]);
    }

    /**
     * Scope for difficult questions.
     */
    public function scopeDifficult($query, float $threshold = 3.5)
    {
        return $query->where('difficulty_rating', '>=', $threshold);
    }
}
