<?php

namespace App\Http\Controllers;

use App\Models\GameAnswer;
use App\Models\GameParticipant;
use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{

    public function storeAnswer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game_session_id' => 'required|exists:game_sessions,id',
            'participant_id' => 'required|exists:game_participants,id',
            'question_id' => 'required|integer',
            'answer_given' => 'required|string',
            'answer_time_seconds' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $gameSession = GameSession::findOrFail($request->game_session_id);
            $participant = GameParticipant::findOrFail($request->participant_id);

            $quizData = json_decode($gameSession->quiz_data, true);
            $questions = $quizData['questions'] ?? [];
            $questionData = collect($questions)->firstWhere('id', $request->question_id);

            if (!$questionData) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found in quiz data'
                ], 404);
            }

            $isCorrect = strtoupper(trim($request->answer_given)) === strtoupper(trim($questionData['correct']));

            $gameSettings = json_decode($gameSession->game_settings, true);
            $basePoints = $gameSettings['points_per_question'] ?? 100;
            $timeLimit = $gameSettings['time_per_question'] ?? 30;

            $pointsEarned = 0;
            if ($isCorrect) {
                $timeRatio = $request->answer_time_seconds / $timeLimit;
                if ($timeRatio <= 0.25) {
                    $pointsEarned = $basePoints;
                } elseif ($timeRatio <= 0.5) {
                    $pointsEarned = round($basePoints * 0.9);
                } elseif ($timeRatio <= 0.75) {
                    $pointsEarned = round($basePoints * 0.75);
                } else {
                    $pointsEarned = round($basePoints * 0.5);
                }
            }


            $recentAnswers = GameAnswer::where('participant_id', $participant->id)
                ->where('game_session_id', $gameSession->id)
                ->orderBy('answered_at', 'desc')
                ->limit(2)
                ->pluck('is_correct')
                ->all();

            $hasStreak = count($recentAnswers) >= 2 &&
                        $recentAnswers[0] &&
                        $recentAnswers[1] &&
                        $isCorrect;

            if ($hasStreak) {
                $pointsEarned += 100;
            }


            $answerOrder = GameAnswer::where('game_session_id', $gameSession->id)
                ->where('question_id', $request->question_id)
                ->count() + 1;


            $gameAnswer = GameAnswer::create([
                'game_session_id' => $request->game_session_id,
                'question_id' => $request->question_id,
                'participant_id' => $request->participant_id,
                'answer_given' => $request->answer_given,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'answer_time_seconds' => $request->answer_time_seconds,
                'answer_order' => $answerOrder,
                'got_streak_bonus' => $hasStreak,
                'answered_at' => now(),
            ]);


            $totalScore = GameAnswer::where('participant_id', $participant->id)
                ->where('game_session_id', $gameSession->id)
                ->sum('points_earned');

            $participant->update([
                'total_score' => $totalScore,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully',
                'data' => [
                    'game_answer' => $gameAnswer,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'has_streak_bonus' => $hasStreak,
                    'total_score' => $totalScore,
                    'answer_order' => $answerOrder,
                    'correct_answer' => $questionData['correct'],
                    'explanation' => $questionData['explanation'] ?? null,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store game answer: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save answer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getParticipantAnswers($gameSessionId, $participantId)
    {
        try {
            $answers = GameAnswer::where('game_session_id', $gameSessionId)
                ->where('participant_id', $participantId)
                ->orderBy('answered_at')
                ->get();

            $totalScore = $answers->sum('points_earned');
            $correctAnswers = $answers->where('is_correct', true)->count();
            $totalAnswers = $answers->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'answers' => $answers,
                    'statistics' => [
                        'total_score' => $totalScore,
                        'correct_answers' => $correctAnswers,
                        'total_answers' => $totalAnswers,
                        'accuracy' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get participant answers: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve answers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLeaderboard($gameSessionId)
    {
        try {
            $participants = GameParticipant::where('game_session_id', $gameSessionId)
                ->with('user')
                ->orderBy('total_score', 'desc')
                ->orderBy('updated_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'leaderboard' => $participants->map(function ($participant, $index) {
                        return [
                            'rank' => $index + 1,
                            'participant_id' => $participant->id,
                            'user_name' => $participant->user->name ?? $participant->nickname,
                            'nickname' => $participant->nickname,
                            'total_score' => $participant->total_score,
                            'correct_answers' => $participant->correct_answers,
                            'total_questions' => $participant->total_questions_answered,
                            'accuracy' => $participant->accuracy_percentage,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get leaderboard: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leaderboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
