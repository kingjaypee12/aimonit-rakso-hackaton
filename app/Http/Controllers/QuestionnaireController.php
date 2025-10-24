<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuestionnaireController extends Controller
{
    /**
     * Store questionnaire data from N8N
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'content.title' => 'required|string|max:255',
                'content.lesson_id' => 'required',
                'content.source_summary' => 'nullable|string',
                'content.total_questions' => 'required|integer|min:1',
                'content.questions' => 'required|array|min:1',
                'content.questions.*.id' => 'required|integer',
                'content.questions.*.type' => 'required|string|in:multiple_choice,true_false,short_answer,fill_in_the_blank',
                'content.questions.*.difficulty' => 'required|string|in:Easy,Medium,Hard',
                'content.questions.*.topic' => 'required|string',
                'content.questions.*.question' => 'required|string',
                'content.questions.*.correct_answer' => 'required',
                'content.questions.*.explanation' => 'nullable|string',
                'content.questions.*.options' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->input('content');

            // Generate a unique code for the questionnaire
            $code = 'QUEST_' . $data['lesson_id'] . '_' . time();

            // Create the questionnaire
            $questionnaire = Questionnaire::create([
                'code' => $code,
                'questions' => [
                    'title' => $data['title'],
                    'lesson_id' => $data['lesson_id'],
                    'source_summary' => $data['source_summary'] ?? null,
                    'total_questions' => $data['total_questions'],
                    'questions' => $data['questions']
                ],
                'ai_prompt' => $request->input('ai_prompt') // Optional AI prompt if provided
            ]);

            Log::info('Questionnaire created successfully', [
                'questionnaire_id' => $questionnaire->id,
                'code' => $questionnaire->code,
                'lesson_id' => $data['lesson_id'],
                'total_questions' => $data['total_questions']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Questionnaire created successfully',
                'data' => [
                    'id' => $questionnaire->id,
                    'code' => $questionnaire->code,
                    'lesson_id' => $data['lesson_id'],
                    'total_questions' => $data['total_questions']
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create questionnaire', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create questionnaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get questionnaire by code for frontend consumption
     */
    public function show(string $code): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::where('code', $code)->first();

            if (!$questionnaire) {
                return response()->json([
                    'success' => false,
                    'message' => 'Questionnaire not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $questionnaire->id,
                    'code' => $questionnaire->code,
                    'questions' => $questionnaire->questions,
                    'created_at' => $questionnaire->created_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve questionnaire', [
                'code' => $code,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve questionnaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get questionnaire by lesson ID
     */
    public function getByLessonId(string $lessonId): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::whereJsonContains('questions->lesson_id', $lessonId)
                ->latest()
                ->first();

            if (!$questionnaire) {
                return response()->json([
                    'success' => false,
                    'message' => 'No questionnaire found for this lesson'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $questionnaire->id,
                    'code' => $questionnaire->code,
                    'questions' => $questionnaire->questions,
                    'created_at' => $questionnaire->created_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve questionnaire by lesson ID', [
                'lesson_id' => $lessonId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve questionnaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}