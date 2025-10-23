<?php

namespace App\Listeners;

use App\Events\LessonAudioUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CallN8nWebhook implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LessonAudioUploaded $event): void
    {
        $webhookUrl = env('N8N_WEBHOOK_URL');
        
        if (!$webhookUrl) {
            Log::warning('N8N webhook URL not configured');
            return;
        }

        // Get the audio file path from the lesson or audioData
        $audioFilePath = $event->audioData['file_path'] ?? $event->lesson->audio_file_path;
        
        if (!$audioFilePath) {
            Log::warning('No audio file path found for lesson: ' . $event->lesson->id);
            return;
        }

        // Copy audio file to public directory for n8n download
        $publicAudioUrl = $this->copyAudioToPublic($audioFilePath, $event->lesson->id);

        // Get file information
        $fileSize = null;
        $fileName = basename($audioFilePath);
        
        if (Storage::disk('private')->exists($audioFilePath)) {
            $fileSize = Storage::disk('private')->size($audioFilePath);
        }

        $payload = [
            'lesson' => [
                'id' => $event->lesson->id,
                'title' => $event->lesson->title,
                'description' => $event->lesson->description,
                'subject' => $event->lesson->subject,
                'grade_level' => $event->lesson->grade_level,
                'status' => $event->lesson->status,
                'teacher_id' => $event->lesson->teacher_id,
                'created_at' => $event->lesson->created_at->toISOString(),
                'updated_at' => $event->lesson->updated_at->toISOString(),
            ],
            'audio_data' => [
                'file_path' => $audioFilePath,
                'public_url' => $publicAudioUrl,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'duration_minutes' => $event->audioData['duration_minutes'] ?? $event->lesson->duration_minutes,
                'action' => $event->audioData['action'] ?? 'unknown'
            ],
            'event_type' => 'lesson_audio_uploaded',
            'timestamp' => now()->toISOString()
        ];

        try {
            $response = Http::timeout(30)->get($webhookUrl, $payload);
            
            if ($response->successful()) {
                Log::info('N8N webhook called successfully for lesson: ' . $event->lesson->id, [
                    'public_url' => $publicAudioUrl
                ]);
            } else {
                Log::error('N8N webhook failed', [
                    'lesson_id' => $event->lesson->id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('N8N webhook exception', [
                'lesson_id' => $event->lesson->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Copy audio file to public directory for n8n download
     */
    private function copyAudioToPublic(string $privatePath, int $lessonId): ?string
    {
        try {
            if (!Storage::disk('private')->exists($privatePath)) {
                Log::error('Private audio file not found: ' . $privatePath);
                return null;
            }

            // Create public directory if it doesn't exist
            $publicDir = 'audio/lessons';
            if (!Storage::disk('public')->exists($publicDir)) {
                Storage::disk('public')->makeDirectory($publicDir);
            }

            // Generate public filename with lesson ID
            $extension = pathinfo($privatePath, PATHINFO_EXTENSION);
            $publicFileName = "lesson_{$lessonId}_" . time() . ".{$extension}";
            $publicPath = "{$publicDir}/{$publicFileName}";

            // Copy file from private to public storage
            $fileContent = Storage::disk('private')->get($privatePath);
            Storage::disk('public')->put($publicPath, $fileContent);

            // Generate public URL
            $publicUrl = url("storage/{$publicPath}");

            Log::info('Audio file copied to public directory', [
                'lesson_id' => $lessonId,
                'private_path' => $privatePath,
                'public_path' => $publicPath,
                'public_url' => $publicUrl
            ]);

            return $publicUrl;

        } catch (\Exception $e) {
            Log::error('Failed to copy audio file to public directory', [
                'lesson_id' => $lessonId,
                'private_path' => $privatePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
