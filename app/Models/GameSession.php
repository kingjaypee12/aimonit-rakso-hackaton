<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'teacher_id',
        'title',
        'description',
        'game_pin',
        'quiz_data',
        'game_settings',
        'status',
        'started_at',
        'ended_at',
        'current_question_index',
        'current_question_started_at',
        'total_questions',
        'allow_late_join',
    ];

    protected $casts = [
        'quiz_data' => 'array',
        'game_settings' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'current_question_started_at' => 'datetime',
        'current_question_index' => 'integer',
        'total_questions' => 'integer',
        'allow_late_join' => 'boolean',
    ];

    /**
     * Get the lesson that owns the game session.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the teacher that owns the game session.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the questions for the game session.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Scope a query to only include sessions with a specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include active sessions.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'in_progress']);
    }

    /**
     * Check if the game session is waiting to start.
     */
    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    /**
     * Check if the game session is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the game session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Generate a unique 6-digit game PIN.
     */
    public static function generateGamePin(): string
    {
        do {
            $pin = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('game_pin', $pin)->exists());

        return $pin;
    }

    /**
     * Start the game session.
     */
    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'current_question_started_at' => now(),
        ]);
    }

    /**
     * End the game session.
     */
    public function end(): void
    {
        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);
    }
}
