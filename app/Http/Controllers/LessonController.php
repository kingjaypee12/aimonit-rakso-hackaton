<?php

namespace App\Http\Controllers;

use App\Events\LessonAudioUploaded;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    /**
     * Handle audio recording upload from the lesson form
     */
    public function uploadRecording(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'audio_file' => 'required|file|mimes:webm,mp3,wav,m4a,ogg|max:102400', // 100MB max
                'duration_minutes' => 'required|integer|min:1|max:480', // 8 hours max
                'lesson_id' => 'nullable|exists:lessons,id', // Optional lesson ID for immediate association
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $audioFile = $request->file('audio_file');
            $durationMinutes = $request->input('duration_minutes');
            $lessonId = $request->input('lesson_id');

            // Generate unique filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $userId = Auth::id();
            $originalExtension = $audioFile->getClientOriginalExtension();
            $filename = "lesson_recording_{$userId}_{$timestamp}.{$originalExtension}";

            // Store the file locally
            $filePath = $audioFile->storeAs('lessons/audio', $filename, 'private');

            if (! $filePath) {
                throw new \Exception('Failed to store audio file');
            }

            // Get file size and other metadata
            $fileSize = $audioFile->getSize();
            $mimeType = $audioFile->getMimeType();

            // If lesson_id is provided, update the lesson immediately and fire event
            if ($lessonId) {
                $lesson = Lesson::findOrFail($lessonId);
                $lesson->update([
                    'audio_file_path' => $filePath,
                    'duration_minutes' => $durationMinutes,
                ]);

                // Fire the event for immediate association
                LessonAudioUploaded::dispatch($lesson);
            }

            // Log the upload for debugging
            Log::info('Audio file uploaded successfully', [
                'user_id' => $userId,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'duration_minutes' => $durationMinutes,
                'mime_type' => $mimeType,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Audio file uploaded successfully',
                'data' => [
                    'file_path' => $filePath,
                    'duration' => $durationMinutes,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'filename' => $filename,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Audio upload failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['audio_file']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload audio file: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process uploaded lesson for transcription and quiz generation
     */
    public function processLesson(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'lesson_id' => 'required|exists:lessons,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $lesson = Lesson::findOrFail($request->input('lesson_id'));

            // Check if user has permission to process this lesson
            if ($lesson->teacher_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to process this lesson',
                ], 403);
            }

            // Update lesson status to processing
            $lesson->update(['status' => 'processing']);

            // TODO: Implement actual transcription and quiz generation logic
            // For now, we'll simulate the process

            // In a real implementation, you would:
            // 1. Send audio file to transcription service (e.g., OpenAI Whisper, Google Speech-to-Text)
            // 2. Process transcription to extract key concepts
            // 3. Generate quiz questions based on the content
            // 4. Update lesson with transcription and mark as ready

            Log::info('Lesson processing started', [
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson processing started successfully',
                'data' => [
                    'lesson_id' => $lesson->id,
                    'status' => 'processing',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Lesson processing failed', [
                'error' => $e->getMessage(),
                'lesson_id' => $request->input('lesson_id'),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process lesson: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download audio file
     */
    public function downloadAudio(Lesson $lesson)
    {
        // Check if user has permission to download this audio
        if ($lesson->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized to download this audio file');
        }

        if (! $lesson->audio_file_path) {
            abort(404, 'Audio file not found');
        }

        $filePath = storage_path('app/public/'.$lesson->audio_file_path);

        if (! file_exists($filePath)) {
            abort(404, 'Audio file not found on disk');
        }

        return response()->download($filePath, basename($lesson->audio_file_path));
    }

    /**
     * Stream audio file for playback
     */
    public function streamAudio(Lesson $lesson)
    {
        // Check if user has permission to stream this audio
        if ($lesson->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized to access this audio file');
        }

        if (! $lesson->audio_file_path) {
            abort(404, 'Audio file not found');
        }

        $filePath = storage_path('app/public/'.$lesson->audio_file_path);

        if (! file_exists($filePath)) {
            abort(404, 'Audio file not found on disk');
        }

        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
