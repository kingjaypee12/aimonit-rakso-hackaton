<?php

namespace App\Listeners;

use App\Actions\GenerateFileUrl;
use App\Events\LessonAudioUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        if (! $webhookUrl) {
            Log::warning('N8N webhook URL not configured');

            return;
        }

        // Get the local audio file path and generate external URL
        $audioFilePath = $event->lesson->audio_file_path;
        $externalAudioUrl = null;

        if ($audioFilePath) {
            try {
                $externalAudioUrl = GenerateFileUrl::execute($audioFilePath);

                if ($externalAudioUrl) {
                    Log::info('External audio URL generated successfully', [
                        'lesson_id' => $event->lesson->id,
                        'file_path' => $audioFilePath,
                        'external_url' => $externalAudioUrl,
                    ]);
                } else {
                    Log::warning('Failed to generate external URL for audio file', [
                        'lesson_id' => $event->lesson->id,
                        'file_path' => $audioFilePath,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Exception while generating external URL', [
                    'lesson_id' => $event->lesson->id,
                    'file_path' => $audioFilePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (! $externalAudioUrl) {
            Log::warning('No accessible audio URL found for lesson: '.$event->lesson->id);

            return;
        }

        // Get file information
        $fileName = $audioFilePath ? basename($audioFilePath) : 'unknown';

        // File size will be handled by the external service
        // We could potentially get it from the external URL response headers if needed
        $fileSize = null;

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
                'external_url' => $externalAudioUrl,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'duration_minutes' => $event->lesson->duration_minutes,
            ],
            'auth' => [
                'csrf_token' => csrf_token(),
                'app_url' => config('app.url'),
            ],
            'event_type' => 'lesson_audio_uploaded',
            'timestamp' => now()->toISOString(),
        ];

        try {
            $response = Http::timeout(30)->get($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('N8N webhook called successfully for lesson: '.$event->lesson->id, [
                    'external_url' => $externalAudioUrl,
                ]);
            } else {
                Log::error('N8N webhook failed', [
                    'lesson_id' => $event->lesson->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('N8N webhook exception', [
                'lesson_id' => $event->lesson->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
