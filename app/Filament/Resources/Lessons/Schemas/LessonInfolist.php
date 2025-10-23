<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class LessonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lesson Overview')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->size('lg')
                                    ->weight('bold'),
                                
                                TextEntry::make('status')
                                    ->badge()
                                    ->colors([
                                        'gray' => 'draft',
                                        'warning' => 'processing',
                                        'success' => 'ready',
                                        'primary' => 'published',
                                    ]),
                            ]),
                        
                        TextEntry::make('description')
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                        
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('teacher.name')
                                    ->label('Teacher')
                                    ->icon('heroicon-o-user'),
                                
                                TextEntry::make('subject')
                                    ->placeholder('Not specified')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('grade_level')
                                    ->label('Grade Level')
                                    ->placeholder('Not specified')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ]),

                Section::make('Audio Content')
                    ->schema([
                        TextEntry::make('audio_file_path')
                            ->label('Audio File')
                            ->formatStateUsing(function ($state) {
                                if (!$state) {
                                    return 'No audio file uploaded';
                                }
                                
                                $filename = basename($state);
                                $size = Storage::exists($state) ? 
                                    number_format(Storage::size($state) / 1024 / 1024, 2) . ' MB' : 
                                    'Unknown size';
                                
                                return $filename . ' (' . $size . ')';
                            })
                            ->icon(fn ($state) => $state ? 'heroicon-o-musical-note' : 'heroicon-o-x-mark')
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                        
                        TextEntry::make('duration_minutes')
                            ->label('Duration')
                            ->formatStateUsing(fn ($state) => $state ? $state . ' minutes' : 'Not calculated')
                            ->placeholder('Not calculated'),
                        
                        Actions::make([
                            Action::make('download_audio')
                                ->label('Download Audio')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('primary')
                                ->visible(fn ($record) => !empty($record->audio_file_path))
                                ->url(fn ($record) => Storage::url($record->audio_file_path))
                                ->openUrlInNewTab(),
                            
                            Action::make('play_audio')
                                ->label('Play Audio')
                                ->icon('heroicon-o-play')
                                ->color('success')
                                ->visible(fn ($record) => !empty($record->audio_file_path))
                                ->action(function ($record) {
                                    // This will be handled by JavaScript
                                    return null;
                                })
                                ->extraAttributes(function ($record) {
                                    return [
                                        'onclick' => 'playAudio(this)',
                                        'data-audio-url' => Storage::url($record->audio_file_path ?? '')
                                    ];
                                }),
                        ]),
                    ])
                    ->visible(fn ($record) => !empty($record->audio_file_path)),

                Section::make('Transcription')
                    ->schema([
                        TextEntry::make('transcription')
                            ->label('')
                            ->placeholder('Transcription will appear here after audio processing...')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->transcription))
                    ->collapsible(),

                Section::make('Quiz Generation')
                    ->schema([
                        Actions::make([
                            Action::make('create_quiz')
                                ->label('Create Gamified Quiz')
                                ->icon('heroicon-o-puzzle-piece')
                                ->color('success')
                                ->size('lg')
                                ->visible(fn ($record) => $record->status === 'ready')
                                ->action(function ($record) {
                                    return redirect()->route('filament.admin.resources.game-sessions.create', [
                                        'lesson_id' => $record->id
                                    ]);
                                }),
                            
                            Action::make('view_quizzes')
                                ->label('View Existing Quizzes')
                                ->icon('heroicon-o-eye')
                                ->color('primary')
                                ->outlined()
                                ->visible(fn ($record) => $record->gameSessions()->exists())
                                ->action(function ($record) {
                                    return redirect()->route('filament.admin.resources.game-sessions.index', [
                                        'tableFilters[lesson_id][value]' => $record->id
                                    ]);
                                }),
                        ])
                        ->alignment('center'),
                    ])
                    ->visible(fn ($record) => $record->status === 'ready' || $record->gameSessions()->exists()),

                Section::make('Metadata')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime('F j, Y \a\t g:i A'),
                                
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('F j, Y \a\t g:i A'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
