<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'participant_id',
        'achievement_type',
        'achievement_name',
        'description',
        'icon',
        'bonus_points',
        'metadata',
        'earned_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'bonus_points' => 'integer',
        'earned_at' => 'datetime',
    ];

    // Achievement types constants
    public const TYPE_FASTEST_ANSWER = 'fastest_answer';
    public const TYPE_PERFECT_SCORE = 'perfect_score';
    public const TYPE_COMEBACK_KING = 'comeback_king';
    public const TYPE_STREAK_MASTER = 'streak_master';
    public const TYPE_FIRST_PLACE = 'first_place';
    public const TYPE_PARTICIPATION = 'participation';
    public const TYPE_IMPROVEMENT = 'improvement';

    /**
     * Get the game session that this achievement belongs to.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the participant who earned this achievement.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(GameParticipant::class, 'participant_id');
    }

    /**
     * Get the student who earned this achievement through the participant.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id', 'id')
            ->through('participant');
    }

    /**
     * Create a fastest answer achievement.
     */
    public static function createFastestAnswer(GameParticipant $participant, array $metadata = []): self
    {
        return self::create([
            'game_session_id' => $participant->game_session_id,
            'participant_id' => $participant->id,
            'achievement_type' => self::TYPE_FASTEST_ANSWER,
            'achievement_name' => 'Lightning Fast',
            'description' => 'Fastest answer in the game session',
            'icon' => '⚡',
            'bonus_points' => 50,
            'metadata' => $metadata,
            'earned_at' => now(),
        ]);
    }

    /**
     * Create a perfect score achievement.
     */
    public static function createPerfectScore(GameParticipant $participant, array $metadata = []): self
    {
        return self::create([
            'game_session_id' => $participant->game_session_id,
            'participant_id' => $participant->id,
            'achievement_type' => self::TYPE_PERFECT_SCORE,
            'achievement_name' => 'Perfect Score',
            'description' => 'Answered all questions correctly',
            'icon' => '🎯',
            'bonus_points' => 100,
            'metadata' => $metadata,
            'earned_at' => now(),
        ]);
    }

    /**
     * Create a comeback king achievement.
     */
    public static function createComebackKing(GameParticipant $participant, array $metadata = []): self
    {
        return self::create([
            'game_session_id' => $participant->game_session_id,
            'participant_id' => $participant->id,
            'achievement_type' => self::TYPE_COMEBACK_KING,
            'achievement_name' => 'Comeback King',
            'description' => 'Made an impressive comeback from behind',
            'icon' => '👑',
            'bonus_points' => 75,
            'metadata' => $metadata,
            'earned_at' => now(),
        ]);
    }

    /**
     * Create a streak master achievement.
     */
    public static function createStreakMaster(GameParticipant $participant, int $streakLength, array $metadata = []): self
    {
        return self::create([
            'game_session_id' => $participant->game_session_id,
            'participant_id' => $participant->id,
            'achievement_type' => self::TYPE_STREAK_MASTER,
            'achievement_name' => 'Streak Master',
            'description' => "Achieved a {$streakLength}-question streak",
            'icon' => '🔥',
            'bonus_points' => $streakLength * 10,
            'metadata' => array_merge($metadata, ['streak_length' => $streakLength]),
            'earned_at' => now(),
        ]);
    }

    /**
     * Get achievements by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('achievement_type', $type);
    }

    /**
     * Get achievements for a specific game session.
     */
    public function scopeForGameSession($query, int $gameSessionId)
    {
        return $query->where('game_session_id', $gameSessionId);
    }

    /**
     * Get achievements for a specific participant.
     */
    public function scopeForParticipant($query, int $participantId)
    {
        return $query->where('participant_id', $participantId);
    }

    /**
     * Get recent achievements.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('earned_at', '>=', now()->subDays($days));
    }

    /**
     * Get the formatted achievement display.
     */
    public function getFormattedDisplayAttribute(): string
    {
        return "{$this->icon} {$this->achievement_name}";
    }

    /**
     * Check if this achievement has bonus points.
     */
    public function hasBonusPoints(): bool
    {
        return $this->bonus_points > 0;
    }
}