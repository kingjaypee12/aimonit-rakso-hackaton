<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Events\LessonAudioUploaded;
use App\Filament\Resources\Lessons\LessonResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Fire event if lesson has audio file and it was just added/updated
        if ($this->record->audio_file_path && $this->record->wasChanged('audio_file_path')) {
            LessonAudioUploaded::dispatch($this->record, [
                'file_path' => $this->record->audio_file_path,
                'duration_minutes' => $this->record->duration_minutes,
                'action' => 'updated'
            ]);
        }
    }
}
