<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Events\LessonAudioUploaded;
use App\Filament\Resources\Lessons\LessonResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;

    protected function afterCreate(): void
    {
        // Fire event if lesson has audio file
        if ($this->record->audio_file_path) {
            LessonAudioUploaded::dispatch($this->record, [
                'file_path' => $this->record->audio_file_path,
                'duration_minutes' => $this->record->duration_minutes,
                'action' => 'created'
            ]);
        }
    }
}
