<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\Lesson;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GameSessionController extends Controller
{
    /**
     * Create a new game session for a lesson
     */
    public function createSession(Request $request, Lesson $lesson): JsonResponse
    {
        try {
            // Validate that the lesson is ready for game sessions
            if ($lesson->status !== 'ready') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lesson must be in "ready" status to create a game session.'
                ], 400);
            }

            // Check if there's a questionnaire for this lesson
            $questionnaire = Questionnaire::where('code', 'LIKE', "QUEST_{$lesson->id}_%")
                ->latest()
                ->first();

            if (!$questionnaire) {
                return response()->json([
                    'success' => false,
                    'message' => 'No questionnaire found for this lesson. Please generate questions first.'
                ], 400);
            }

            // Generate unique game PIN
            $gamePin = GameSession::generateGamePin();

            // Default game settings
            $gameSettings = [
                'answer_time_limit' => 30, // seconds
                'points_per_correct' => 100,
                'streak_bonus' => 50,
                'randomize_questions' => false,
                'show_correct_answer' => true,
                'allow_late_join' => true,
            ];

            // Create the game session
            $gameSession = GameSession::create([
                'lesson_id' => $lesson->id,
                'teacher_id' => Auth::id(),
                'title' => "Quiz: {$lesson->title}",
                'description' => "Interactive quiz session for {$lesson->title}",
                'topic' => $lesson->subject ?? 'General',
                'game_pin' => $gamePin,
                'quiz_data' => $questionnaire->questions,
                'game_settings' => $gameSettings,
                'total_questions' => count($questionnaire->questions),
                'status' => 'waiting',
                'allow_late_join' => true,
            ]);

            Log::info('Game session created', [
                'session_id' => $gameSession->id,
                'lesson_id' => $lesson->id,
                'teacher_id' => Auth::id(),
                'game_pin' => $gamePin
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Game session created successfully!',
                'data' => [
                    'session_id' => $gameSession->id,
                    'game_pin' => $gamePin,
                    'title' => $gameSession->title,
                    'total_questions' => $gameSession->total_questions,
                    'status' => $gameSession->status,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create game session', [
                'lesson_id' => $lesson->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create game session. Please try again.'
            ], 500);
        }
    }

    /**
     * Get game session details
     */
    public function getSession(GameSession $gameSession): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $gameSession->id,
                'title' => $gameSession->title,
                'description' => $gameSession->description,
                'game_pin' => $gameSession->game_pin,
                'status' => $gameSession->status,
                'total_questions' => $gameSession->total_questions,
                'lesson' => [
                    'id' => $gameSession->lesson->id,
                    'title' => $gameSession->lesson->title,
                ],
                'created_at' => $gameSession->created_at,
                'started_at' => $gameSession->started_at,
                'ended_at' => $gameSession->ended_at,
            ]
        ]);
    }
}