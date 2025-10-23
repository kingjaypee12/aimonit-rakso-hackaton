<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Actions\UploadFile;
use App\Events\LessonAudioUploaded;
use App\Filament\Resources\Lessons\LessonResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        // Separate audio file uploading logic
        if (isset($data['audio_file_path']) && $data['audio_file_path']) {
            // Use UploadFile action to upload the audio file and get the generated file path
                $uploadFile = new UploadFile();
                $audioPath = $uploadFile->execute($data['audio_file_path']);


                // Save the generated file path as audio_file_path
                $data['audio_file_path'] = $audioPath;
        }

        // Create the lesson record with the updated data
        return static::getModel()::create($data);
    }

    protected function afterCreate(): void
    {
        // Dispatch event for audio file if present
        if ($this->record->audio_file_path) {
            LessonAudioUploaded::dispatch($this->record);
        }
    }
}
