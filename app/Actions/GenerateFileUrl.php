<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class GenerateFileUrl
{
    public static function execute($file)
    {
        // If file is null or empty, return null
        if (empty($file)) {
            \Illuminate\Support\Facades\Log::warning('GenerateFileUrl: Empty file path provided');

            return null;
        }

        // Log the file path for debugging
        \Illuminate\Support\Facades\Log::info('GenerateFileUrl: Generating URL for file: '.$file);

        $response = Http::withHeaders([
            'x-api-key' => env('UPLOAD_API_KEY'),
            'x-api-secret' => env('UPLOAD_API_SECRET'),
        ])->get(env('UPLOAD_API_URL').'/api/get', ['filepath' => $file]);

        if ($response->successful()) {
            \Illuminate\Support\Facades\Log::info('GenerateFileUrl: API response', $response->json());

            if ($response->json()['status'] == 'success') {
                return $response->json()['temporary_url'];
            }

            \Illuminate\Support\Facades\Log::warning('GenerateFileUrl: API returned non-success status', $response->json());

            return null;
        } else {
            // Request failed
            \Illuminate\Support\Facades\Log::error('GenerateFileUrl: API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response->throw();
        }
    }
}
