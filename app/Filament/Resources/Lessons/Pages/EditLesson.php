<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Events\LessonAudioUploaded;
use App\Filament\Resources\Lessons\LessonResource;
use Filament\Actions;
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
        // Dispatch event for audio file changes
        if ($this->record->audio_file_path && $this->record->wasChanged('audio_file_path')) {
            LessonAudioUploaded::dispatch($this->record);
        }
    }
}
