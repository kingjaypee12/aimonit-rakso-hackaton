<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Events\LessonAudioUploaded;
use App\Filament\Resources\Lessons\LessonResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;

    protected function handleRecordCreation(array $data): Model
    {
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
