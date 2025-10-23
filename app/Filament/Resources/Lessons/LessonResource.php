<?php

namespace App\Filament\Resources\Lessons;

use App\Filament\Resources\Lessons\Pages\CreateLesson;
use App\Filament\Resources\Lessons\Pages\EditLesson;
use App\Filament\Resources\Lessons\Pages\ListLessons;
use App\Filament\Resources\Lessons\Pages\ViewLesson;
use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Filament\Resources\Lessons\Schemas\LessonInfolist;
use App\Filament\Resources\Lessons\Tables\LessonsTable;
use App\Models\Lesson;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $navigationLabel = 'Lessons';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LessonInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // Future: Add GameSessionsRelationManager here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLessons::route('/'),
            'create' => CreateLesson::route('/create'),
            'view' => ViewLesson::route('/{record}'),
            'edit' => EditLesson::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['teacher']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description', 'subject', 'teacher.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Teacher' => $record->teacher?->name,
            'Subject' => $record->subject,
            'Status' => ucfirst($record->status),
        ];
    }
}
