<?php

namespace App\Listeners;

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
        $webhookUrl = config('services.n8n.webhook_url');
        
        if (!$webhookUrl) {
            Log::warning('N8N webhook URL not configured');
            return;
        }

        $payload = [
            'lesson_id' => $event->lesson->id,
            'lesson_title' => $event->lesson->title,
            'lesson_description' => $event->lesson->description,
            'audio_file' => $event->audioData['filename'],
            'audio_path' => $event->audioData['path'],
            'audio_size' => $event->audioData['size'],
            'audio_duration' => $event->audioData['duration'] ?? null,
            'created_at' => $event->lesson->created_at->toISOString(),
            'event_type' => 'lesson_audio_uploaded'
        ];

        try {
            $response = Http::timeout(30)->post($webhookUrl, $payload);
            
            if ($response->successful()) {
                Log::info('N8N webhook called successfully for lesson: ' . $event->lesson->id);
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
}
