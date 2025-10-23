<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'teacher_id',
        'overall_statistics',
        'question_analysis',
        'student_performance',
        'topic_comprehension',
        'podium_winners',
        'class_average_score',
        'total_participants',
        'questions_completed',
        'total_answers_submitted',
        'average_completion_time',
        'recommendations',
        'generated_at',
    ];

    protected $casts = [
        'overall_statistics' => 'array',
        'question_analysis' => 'array',
        'student_performance' => 'array',
        'topic_comprehension' => 'array',
        'podium_winners' => 'array',
        'class_average_score' => 'decimal:2',
        'total_participants' => 'integer',
        'questions_completed' => 'integer',
        'total_answers_submitted' => 'integer',
        'average_completion_time' => 'decimal:2',
        'recommendations' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the game session that this report belongs to.
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the teacher who owns this report.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Generate a comprehensive report for a game session.
     */
    public static function generateReport(int $gameSessionId): self
    {
        $gameSession = GameSession::with(['lesson', 'teacher', 'questions'])->findOrFail($gameSessionId);
        $participants = GameParticipant::where('game_session_id', $gameSessionId)->get();
        $answers = GameAnswer::where('game_session_id', $gameSessionId)->get();
        $questionResults = QuestionResult::where('game_session_id', $gameSessionId)->get();
        $finalLeaderboard = GameLeaderboard::where('game_session_id', $gameSessionId)
            ->where('type', 'final')
            ->latest()
            ->first();

        // Overall Statistics
        $overallStats = [
            'total_participants' => $participants->count(),
            'completion_rate' => $participants->where('is_active', false)->count() / max($participants->count(), 1) * 100,
            'average_score' => $participants->avg('total_score'),
            'highest_score' => $participants->max('total_score'),
            'lowest_score' => $participants->min('total_score'),
            'average_accuracy' => $participants->avg('accuracy'),
            'total_questions' => $gameSession->questions->count(),
            'questions_completed' => $questionResults->count(),
            'total_answers_submitted' => $answers->count(),
            'average_answer_time' => $answers->avg('answer_time_seconds'),
        ];

        // Question Analysis
        $questionAnalysis = $questionResults->map(function ($result) {
            return [
                'question_id' => $result->question_id,
                'question_text' => $result->question->question_text,
                'accuracy_percentage' => $result->accuracy_percentage,
                'difficulty_level' => $result->difficulty_level,
                'difficulty_rating' => $result->difficulty_rating,
                'average_answer_time' => $result->average_answer_time,
                'fastest_answer_time' => $result->fastest_answer_time,
                'fastest_answer_by' => $result->fastest_answer_by,
                'answer_distribution' => $result->answer_distribution,
                'insights' => $result->getComprehensionInsights(),
            ];
        })->toArray();

        // Student Performance
        $studentPerformance = $participants->map(function ($participant) {
            return [
                'student_id' => $participant->student_id,
                'display_name' => $participant->display_name,
                'total_score' => $participant->total_score,
                'accuracy' => $participant->accuracy,
                'correct_answers' => $participant->correct_answers,
                'incorrect_answers' => $participant->incorrect_answers,
                'longest_streak' => $participant->longest_streak,
                'average_answer_time' => $participant->average_answer_time,
                'rank' => $participant->rank,
                'comprehension_level' => self::calculateComprehensionLevel($participant->accuracy),
                'strengths' => self::identifyStrengths($participant),
                'areas_for_improvement' => self::identifyImprovements($participant),
            ];
        })->toArray();

        // Topic Comprehension (based on lesson subject and question analysis)
        $topicComprehension = [
            'subject' => $gameSession->lesson->subject,
            'grade_level' => $gameSession->lesson->grade_level,
            'overall_comprehension' => self::calculateOverallComprehension($questionResults),
            'difficult_concepts' => self::identifyDifficultConcepts($questionResults),
            'well_understood_concepts' => self::identifyWellUnderstoodConcepts($questionResults),
        ];

        // Podium Winners
        $podiumWinners = $finalLeaderboard ? $finalLeaderboard->getPodiumWinners() : [
            'first' => null,
            'second' => null,
            'third' => null,
        ];

        // AI-Generated Recommendations
        $recommendations = self::generateRecommendations($overallStats, $questionAnalysis, $studentPerformance);

        return self::create([
            'game_session_id' => $gameSessionId,
            'teacher_id' => $gameSession->teacher_id,
            'overall_statistics' => $overallStats,
            'question_analysis' => $questionAnalysis,
            'student_performance' => $studentPerformance,
            'topic_comprehension' => $topicComprehension,
            'podium_winners' => $podiumWinners,
            'class_average_score' => $overallStats['average_score'],
            'total_participants' => $overallStats['total_participants'],
            'questions_completed' => $overallStats['questions_completed'],
            'total_answers_submitted' => $overallStats['total_answers_submitted'],
            'average_completion_time' => $overallStats['average_answer_time'],
            'recommendations' => $recommendations,
            'generated_at' => now(),
        ]);
    }

    /**
     * Calculate comprehension level based on accuracy.
     */
    protected static function calculateComprehensionLevel(float $accuracy): string
    {
        if ($accuracy >= 90) {
            return 'Excellent';
        } elseif ($accuracy >= 80) {
            return 'Good';
        } elseif ($accuracy >= 70) {
            return 'Satisfactory';
        } elseif ($accuracy >= 60) {
            return 'Needs Improvement';
        } else {
            return 'Requires Attention';
        }
    }

    /**
     * Identify student strengths.
     */
    protected static function identifyStrengths(GameParticipant $participant): array
    {
        $strengths = [];

        if ($participant->accuracy >= 80) {
            $strengths[] = 'High accuracy in answering questions';
        }

        if ($participant->longest_streak >= 5) {
            $strengths[] = 'Consistent performance with long answer streaks';
        }

        if ($participant->average_answer_time <= 10) {
            $strengths[] = 'Quick thinking and fast response times';
        }

        return $strengths;
    }

    /**
     * Identify areas for improvement.
     */
    protected static function identifyImprovements(GameParticipant $participant): array
    {
        $improvements = [];

        if ($participant->accuracy < 60) {
            $improvements[] = 'Focus on understanding core concepts';
        }

        if ($participant->longest_streak <= 2) {
            $improvements[] = 'Work on consistency and concentration';
        }

        if ($participant->average_answer_time > 30) {
            $improvements[] = 'Practice to improve response speed';
        }

        return $improvements;
    }

    /**
     * Calculate overall comprehension for the topic.
     */
    protected static function calculateOverallComprehension($questionResults): float
    {
        if ($questionResults->isEmpty()) {
            return 0;
        }

        return round($questionResults->avg('accuracy_percentage'), 2);
    }

    /**
     * Identify difficult concepts.
     */
    protected static function identifyDifficultConcepts($questionResults): array
    {
        return $questionResults->where('accuracy_percentage', '<', 60)
            ->pluck('question.question_text')
            ->toArray();
    }

    /**
     * Identify well understood concepts.
     */
    protected static function identifyWellUnderstoodConcepts($questionResults): array
    {
        return $questionResults->where('accuracy_percentage', '>=', 80)
            ->pluck('question.question_text')
            ->toArray();
    }

    /**
     * Generate AI-powered recommendations.
     */
    protected static function generateRecommendations(array $overallStats, array $questionAnalysis, array $studentPerformance): array
    {
        $recommendations = [];

        // Class-level recommendations
        if ($overallStats['average_accuracy'] < 70) {
            $recommendations[] = [
                'type' => 'class',
                'priority' => 'high',
                'message' => 'Consider reviewing the lesson material as the class average accuracy is below 70%.',
                'action' => 'Schedule a review session focusing on the most challenging concepts.',
            ];
        }

        // Question-specific recommendations
        $difficultQuestions = array_filter($questionAnalysis, fn ($q) => $q['accuracy_percentage'] < 50);
        if (! empty($difficultQuestions)) {
            $recommendations[] = [
                'type' => 'content',
                'priority' => 'medium',
                'message' => 'Several questions had low accuracy rates. Consider revising these topics.',
                'action' => 'Create additional practice materials for difficult concepts.',
                'questions' => array_column($difficultQuestions, 'question_text'),
            ];
        }

        // Individual student recommendations
        $strugglingStudents = array_filter($studentPerformance, fn ($s) => $s['accuracy'] < 60);
        if (! empty($strugglingStudents)) {
            $recommendations[] = [
                'type' => 'individual',
                'priority' => 'high',
                'message' => 'Some students may need additional support.',
                'action' => 'Consider one-on-one sessions or additional practice for struggling students.',
                'students' => array_column($strugglingStudents, 'display_name'),
            ];
        }

        return $recommendations;
    }

    /**
     * Get summary statistics.
     */
    public function getSummary(): array
    {
        return [
            'class_performance' => $this->calculateComprehensionLevel($this->class_average_score),
            'participation_rate' => round(($this->total_participants / max($this->gameSession->participants()->count(), 1)) * 100, 2),
            'completion_rate' => round(($this->questions_completed / max($this->gameSession->questions()->count(), 1)) * 100, 2),
            'engagement_level' => $this->calculateEngagementLevel(),
        ];
    }

    /**
     * Calculate engagement level based on various metrics.
     */
    protected function calculateEngagementLevel(): string
    {
        $participationRate = ($this->total_participants / max($this->gameSession->participants()->count(), 1)) * 100;
        $completionRate = ($this->questions_completed / max($this->gameSession->questions()->count(), 1)) * 100;

        $engagementScore = ($participationRate + $completionRate) / 2;

        if ($engagementScore >= 90) {
            return 'Very High';
        } elseif ($engagementScore >= 75) {
            return 'High';
        } elseif ($engagementScore >= 60) {
            return 'Moderate';
        } else {
            return 'Low';
        }
    }
}
