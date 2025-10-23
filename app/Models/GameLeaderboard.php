<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameLeaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'question_id',
        'rankings',
        'type',
        'snapshot_at',
    ];

    protected $casts = [
        'rankings' => 'array',
        'snapshot_at' => 'datetime',
    ];

    /**
     * Get the game session that this leaderboard belongs to.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the question that this leaderboard is for (if applicable).
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Create a current leaderboard snapshot.
     */
    public static function createCurrentSnapshot(int $gameSessionId): self
    {
        $participants = GameParticipant::where('game_session_id', $gameSessionId)
            ->where('is_active', true)
            ->orderByDesc('total_score')
            ->orderBy('average_answer_time')
            ->get();

        $rankings = $participants->map(function ($participant, $index) {
            return [
                'rank' => $index + 1,
                'participant_id' => $participant->id,
                'student_id' => $participant->student_id,
                'display_name' => $participant->display_name,
                'total_score' => $participant->total_score,
                'correct_answers' => $participant->correct_answers,
                'incorrect_answers' => $participant->incorrect_answers,
                'current_streak' => $participant->current_streak,
                'longest_streak' => $participant->longest_streak,
                'average_answer_time' => $participant->average_answer_time,
                'accuracy' => $participant->accuracy,
            ];
        })->toArray();

        return self::create([
            'game_session_id' => $gameSessionId,
            'rankings' => $rankings,
            'type' => 'current',
            'snapshot_at' => now(),
        ]);
    }

    /**
     * Create a question-specific leaderboard snapshot.
     */
    public static function createQuestionSnapshot(int $gameSessionId, int $questionId): self
    {
        $answers = GameAnswer::where('game_session_id', $gameSessionId)
            ->where('question_id', $questionId)
            ->with('participant')
            ->orderBy('answer_time_seconds')
            ->get();

        $rankings = $answers->map(function ($answer, $index) {
            return [
                'rank' => $index + 1,
                'participant_id' => $answer->participant_id,
                'student_id' => $answer->participant->student_id,
                'display_name' => $answer->participant->display_name,
                'answer_given' => $answer->answer_given,
                'is_correct' => $answer->is_correct,
                'points_earned' => $answer->points_earned,
                'answer_time_seconds' => $answer->answer_time_seconds,
                'got_streak_bonus' => $answer->got_streak_bonus,
            ];
        })->toArray();

        return self::create([
            'game_session_id' => $gameSessionId,
            'question_id' => $questionId,
            'rankings' => $rankings,
            'type' => 'question',
            'snapshot_at' => now(),
        ]);
    }

    /**
     * Create a final leaderboard snapshot.
     */
    public static function createFinalSnapshot(int $gameSessionId): self
    {
        $participants = GameParticipant::where('game_session_id', $gameSessionId)
            ->orderByDesc('total_score')
            ->orderBy('average_answer_time')
            ->get();

        $rankings = $participants->map(function ($participant, $index) {
            return [
                'rank' => $index + 1,
                'participant_id' => $participant->id,
                'student_id' => $participant->student_id,
                'display_name' => $participant->display_name,
                'total_score' => $participant->total_score,
                'correct_answers' => $participant->correct_answers,
                'incorrect_answers' => $participant->incorrect_answers,
                'longest_streak' => $participant->longest_streak,
                'average_answer_time' => $participant->average_answer_time,
                'accuracy' => $participant->accuracy,
                'joined_at' => $participant->joined_at,
                'left_at' => $participant->left_at,
                'is_active' => $participant->is_active,
            ];
        })->toArray();

        return self::create([
            'game_session_id' => $gameSessionId,
            'rankings' => $rankings,
            'type' => 'final',
            'snapshot_at' => now(),
        ]);
    }

    /**
     * Get the top N participants from the rankings.
     */
    public function getTopParticipants(int $limit = 3): array
    {
        return array_slice($this->rankings, 0, $limit);
    }

    /**
     * Get a specific participant's ranking.
     */
    public function getParticipantRanking(int $participantId): ?array
    {
        foreach ($this->rankings as $ranking) {
            if ($ranking['participant_id'] === $participantId) {
                return $ranking;
            }
        }

        return null;
    }

    /**
     * Get the podium winners (top 3).
     */
    public function getPodiumWinners(): array
    {
        $top3 = $this->getTopParticipants(3);

        return [
            'first' => $top3[0] ?? null,
            'second' => $top3[1] ?? null,
            'third' => $top3[2] ?? null,
        ];
    }

    /**
     * Get statistics from the leaderboard.
     */
    public function getStatistics(): array
    {
        if (empty($this->rankings)) {
            return [
                'total_participants' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'average_accuracy' => 0,
            ];
        }

        $scores = array_column($this->rankings, 'total_score');
        $accuracies = array_column($this->rankings, 'accuracy');

        return [
            'total_participants' => count($this->rankings),
            'average_score' => round(array_sum($scores) / count($scores), 2),
            'highest_score' => max($scores),
            'lowest_score' => min($scores),
            'average_accuracy' => round(array_sum($accuracies) / count($accuracies), 2),
        ];
    }

    /**
     * Scope for current leaderboards.
     */
    public function scopeCurrent($query)
    {
        return $query->where('type', 'current');
    }

    /**
     * Scope for final leaderboards.
     */
    public function scopeFinal($query)
    {
        return $query->where('type', 'final');
    }

    /**
     * Scope for question-specific leaderboards.
     */
    public function scopeQuestion($query)
    {
        return $query->where('type', 'question');
    }

    /**
     * Scope for latest snapshot.
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('snapshot_at');
    }
}
