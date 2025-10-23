<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lesson Information')
                    ->description('Basic information about the lesson')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('teacher_id')
                                    ->relationship('teacher', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->default(auth()->id()),

                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'processing' => 'Processing',
                                        'ready' => 'Ready',
                                        'published' => 'Published',
                                    ])
                                    ->required()
                                    ->default('draft')
                                    ->native(false),
                            ]),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter lesson title'),

                        Textarea::make('description')
                            ->placeholder('Describe what this lesson covers...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('subject')
                                    ->placeholder('e.g., Mathematics, Science')
                                    ->maxLength(100),

                                TextInput::make('grade_level')
                                    ->placeholder('e.g., Grade 5, High School')
                                    ->maxLength(50),
                            ]),
                    ]),

                Section::make('Lesson Content')
                    ->description('Upload audio/video files or record live class')
                    ->schema([
                        FileUpload::make('audio_file_path')
                            ->label('Audio/Video File')
                            ->acceptedFileTypes(['audio/*', 'video/*'])
                            ->maxSize(500 * 1024) // 500MB
                            //->maxSize(1024)
                            ->storeFiles(false)
                            ->previewable(false)
                            ->helperText('Upload an audio or video file of the lesson (max 500MB). External upload will happen when you save the lesson.')
                            ->columnSpanFull(),

                        Actions::make([
                            Action::make('record_audio')
                                ->label('Record Live Class')
                                ->icon('heroicon-o-microphone')
                                ->iconPosition(IconPosition::Before)
                                ->color('success')
                                ->outlined()
                                ->action(function () {
                                    // Modal will be handled by JavaScript
                                    return null;
                                })
                                ->extraAttributes([
                                    'onclick' => 'openRecordingModal()',
                                    'id' => 'record-button',
                                ]),
                        ])
                            ->columnSpanFull()
                            ->alignment('center'),

                        TextEntry::make('recording_info')
                            ->label('')
                            ->state('Click "Record Live Class" to start recording audio directly from your microphone. The recording will be automatically saved and processed.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Audio Information')
                    ->description('Audio file details and status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('duration_minutes')
                                    ->label('Duration (minutes)')
                                    ->numeric()
                                    ->disabled()
                                    ->placeholder('Auto-calculated'),

                                TextEntry::make('processing_status')
                                    ->label('Status')
                                    ->state(fn ($record) => $record ?
                                        match ($record->status) {
                                            'draft' => '⏳ Ready for upload',
                                            'processing' => '🔄 Processing audio...',
                                            'completed' => '✅ Audio saved',
                                            'failed' => '❌ Processing failed',
                                            default => '❓ Unknown status'
                                        } : '⏳ Ready for upload'
                                    ),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => ! $record || $record->status === 'draft'),

                // Recording Modal
                View::make('components.recording-modal')
                    ->columnSpanFull(),
            ])
            ->extraAttributes([
                'x-data' => '{ recordingModal: false }',
            ]);
    }
}
