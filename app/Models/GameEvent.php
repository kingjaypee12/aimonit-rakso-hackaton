<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'event_type',
        'event_data',
        'occurred_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'occurred_at' => 'datetime',
    ];

    // Event type constants
    public const TYPE_GAME_STARTED = 'game_started';
    public const TYPE_QUESTION_SHOWN = 'question_shown';
    public const TYPE_QUESTION_ENDED = 'question_ended';
    public const TYPE_GAME_COMPLETED = 'game_completed';
    public const TYPE_PARTICIPANT_JOINED = 'participant_joined';
    public const TYPE_PARTICIPANT_LEFT = 'participant_left';
    public const TYPE_ANSWER_SUBMITTED = 'answer_submitted';
    public const TYPE_ACHIEVEMENT_EARNED = 'achievement_earned';
    public const TYPE_LEADERBOARD_UPDATE = 'leaderboard_update';

    /**
     * Get the game session that this event belongs to.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Log a game started event.
     */
    public static function logGameStarted(GameSession $gameSession, array $data = []): self
    {
        return self::create([
            'game_session_id' => $gameSession->id,
            'event_type' => self::TYPE_GAME_STARTED,
            'event_data' => array_merge([
                'total_questions' => $gameSession->questions()->count(),
                'participants_count' => $gameSession->participants()->count(),
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log a question shown event.
     */
    public static function logQuestionShown(GameSession $gameSession, Question $question, array $data = []): self
    {
        return self::create([
            'game_session_id' => $gameSession->id,
            'event_type' => self::TYPE_QUESTION_SHOWN,
            'event_data' => array_merge([
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'time_limit' => $question->time_limit_seconds,
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log a question ended event.
     */
    public static function logQuestionEnded(GameSession $gameSession, Question $question, array $data = []): self
    {
        $answers = GameAnswer::where('game_session_id', $gameSession->id)
                            ->where('question_id', $question->id)
                            ->get();

        return self::create([
            'game_session_id' => $gameSession->id,
            'event_type' => self::TYPE_QUESTION_ENDED,
            'event_data' => array_merge([
                'question_id' => $question->id,
                'total_answers' => $answers->count(),
                'correct_answers' => $answers->where('is_correct', true)->count(),
                'average_time' => $answers->avg('answer_time_seconds'),
                'fastest_time' => $answers->min('answer_time_seconds'),
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log a game completed event.
     */
    public static function logGameCompleted(GameSession $gameSession, array $data = []): self
    {
        $participants = $gameSession->participants;
        
        return self::create([
            'game_session_id' => $gameSession->id,
            'event_type' => self::TYPE_GAME_COMPLETED,
            'event_data' => array_merge([
                'duration_minutes' => $gameSession->created_at->diffInMinutes(now()),
                'total_participants' => $participants->count(),
                'questions_completed' => $gameSession->questions()->count(),
                'average_score' => $participants->avg('total_score'),
                'highest_score' => $participants->max('total_score'),
                'winner' => $participants->where('rank', 1)->first()?->nickname,
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log a participant joined event.
     */
    public static function logParticipantJoined(GameSession $gameSession, GameParticipant $participant, array $data = []): self
    {
        return self::create([
            'game_session_id' => $gameSession->id,
            'event_type' => self::TYPE_PARTICIPANT_JOINED,
            'event_data' => array_merge([
                'participant_id' => $participant->id,
                'nickname' => $participant->nickname,
                'student_id' => $participant->student_id,
                'total_participants' => $gameSession->participants()->count(),
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log a participant left event.
     */
    public static function logParticipantLeft(GameSession $gameSession, GameParticipant $participant, array $data = []): self
    {
        return self::create([
            'game_session_id' => $gameSession->id,
            'event_type' => self::TYPE_PARTICIPANT_LEFT,
            'event_data' => array_merge([
                'participant_id' => $participant->id,
                'nickname' => $participant->nickname,
                'final_score' => $participant->total_score,
                'final_rank' => $participant->rank,
                'session_duration' => $participant->joined_at?->diffInMinutes(now()),
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log an answer submitted event.
     */
    public static function logAnswerSubmitted(GameAnswer $answer, array $data = []): self
    {
        return self::create([
            'game_session_id' => $answer->game_session_id,
            'event_type' => self::TYPE_ANSWER_SUBMITTED,
            'event_data' => array_merge([
                'question_id' => $answer->question_id,
                'participant_id' => $answer->participant_id,
                'is_correct' => $answer->is_correct,
                'answer_time' => $answer->answer_time_seconds,
                'points_earned' => $answer->points_earned,
                'streak_bonus' => $answer->got_streak_bonus,
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log an achievement earned event.
     */
    public static function logAchievementEarned(Achievement $achievement, array $data = []): self
    {
        return self::create([
            'game_session_id' => $achievement->game_session_id,
            'event_type' => self::TYPE_ACHIEVEMENT_EARNED,
            'event_data' => array_merge([
                'participant_id' => $achievement->participant_id,
                'achievement_type' => $achievement->achievement_type,
                'achievement_name' => $achievement->achievement_name,
                'bonus_points' => $achievement->bonus_points,
            ], $data),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Get events by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Get events for a specific game session.
     */
    public function scopeForGameSession($query, int $gameSessionId)
    {
        return $query->where('game_session_id', $gameSessionId);
    }

    /**
     * Get events within a time range.
     */
    public function scopeWithinTimeRange($query, $startTime, $endTime)
    {
        return $query->whereBetween('occurred_at', [$startTime, $endTime]);
    }

    /**
     * Get recent events.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('occurred_at', '>=', now()->subHours($hours));
    }

    /**
     * Get events ordered by occurrence time.
     */
    public function scopeOrderByTime($query, string $direction = 'desc')
    {
        return $query->orderBy('occurred_at', $direction);
    }

    /**
     * Get game timeline events (major events only).
     */
    public function scopeTimelineEvents($query)
    {
        return $query->whereIn('event_type', [
            self::TYPE_GAME_STARTED,
            self::TYPE_QUESTION_SHOWN,
            self::TYPE_QUESTION_ENDED,
            self::TYPE_GAME_COMPLETED,
        ]);
    }

    /**
     * Get participant activity events.
     */
    public function scopeParticipantActivity($query)
    {
        return $query->whereIn('event_type', [
            self::TYPE_PARTICIPANT_JOINED,
            self::TYPE_PARTICIPANT_LEFT,
            self::TYPE_ANSWER_SUBMITTED,
            self::TYPE_ACHIEVEMENT_EARNED,
        ]);
    }

    /**
     * Get the formatted event description.
     */
    public function getFormattedDescriptionAttribute(): string
    {
        return match ($this->event_type) {
            self::TYPE_GAME_STARTED => 'Game session started',
            self::TYPE_QUESTION_SHOWN => "Question shown: {$this->event_data['question_text'] ?? 'Unknown'}",
            self::TYPE_QUESTION_ENDED => 'Question time ended',
            self::TYPE_GAME_COMPLETED => 'Game session completed',
            self::TYPE_PARTICIPANT_JOINED => "{$this->event_data['nickname'] ?? 'Student'} joined the game",
            self::TYPE_PARTICIPANT_LEFT => "{$this->event_data['nickname'] ?? 'Student'} left the game",
            self::TYPE_ANSWER_SUBMITTED => 'Answer submitted',
            self::TYPE_ACHIEVEMENT_EARNED => "Achievement earned: {$this->event_data['achievement_name'] ?? 'Unknown'}",
            self::TYPE_LEADERBOARD_UPDATE => 'Leaderboard updated',
            default => 'Unknown event',
        };
    }

    /**
     * Get game session analytics from events.
     */
    public static function getGameAnalytics(int $gameSessionId): array
    {
        $events = self::forGameSession($gameSessionId)->orderByTime('asc')->get();
        
        $startEvent = $events->where('event_type', self::TYPE_GAME_STARTED)->first();
        $endEvent = $events->where('event_type', self::TYPE_GAME_COMPLETED)->first();
        
        $duration = $startEvent && $endEvent 
            ? $startEvent->occurred_at->diffInMinutes($endEvent->occurred_at)
            : null;

        return [
            'total_events' => $events->count(),
            'duration_minutes' => $duration,
            'questions_shown' => $events->where('event_type', self::TYPE_QUESTION_SHOWN)->count(),
            'answers_submitted' => $events->where('event_type', self::TYPE_ANSWER_SUBMITTED)->count(),
            'achievements_earned' => $events->where('event_type', self::TYPE_ACHIEVEMENT_EARNED)->count(),
            'participants_joined' => $events->where('event_type', self::TYPE_PARTICIPANT_JOINED)->count(),
            'participants_left' => $events->where('event_type', self::TYPE_PARTICIPANT_LEFT)->count(),
            'timeline' => $events->timelineEvents()->values(),
        ];
    }
}