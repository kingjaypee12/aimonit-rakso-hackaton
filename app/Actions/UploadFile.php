<?php

namespace App\Actions;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class UploadFile
{
    public function execute($file)
    {

        $mimeType = $file->getMimeType();

        $client = new Client;

        $fileData = File::get($file->getRealPath());

        $filename = $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename();

        $response = $client->post(env('UPLOAD_API_URL').'/api/upload', [
            'headers' => [
                'x-api-key' => env('UPLOAD_API_KEY'),
                'x-api-secret' => env('UPLOAD_API_SECRET'),
            ],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => $fileData,
                    'filename' => $filename,
                    'headers' => ['Content-Type' => $mimeType],
                ],
                [
                    'name' => 'path',
                    'contents' => 'hackathon',
                ],
            ],
        ]);

        return json_decode($response->getBody()->getContents())->filepath;
    }
}
