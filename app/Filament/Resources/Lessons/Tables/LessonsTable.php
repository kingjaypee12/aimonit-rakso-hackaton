<?php

namespace App\Filament\Resources\Lessons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->description ? 
                        \Illuminate\Support\Str::limit($record->description, 50) : null),
                
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('subject')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('grade_level')
                    ->label('Grade')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                
                IconColumn::make('has_audio')
                    ->label('Audio')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->audio_file_path))
                    ->trueIcon('heroicon-o-musical-note')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->numeric()
                    ->sortable()
                    ->suffix(' min')
                    ->placeholder('—')
                    ->toggleable(),
                
                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'processing',
                        'success' => 'ready',
                        'primary' => 'published',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-cog-6-tooth' => 'processing',
                        'heroicon-o-check-circle' => 'ready',
                        'heroicon-o-eye' => 'published',
                    ]),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'processing' => 'Processing',
                        'ready' => 'Ready',
                        'published' => 'Published',
                    ])
                    ->multiple(),
                
                SelectFilter::make('subject')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('has_audio')
                    ->label('Has Audio File')
                    ->options([
                        'yes' => 'With Audio',
                        'no' => 'Without Audio',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query): Builder => $query->whereNotNull('audio_file_path'),
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query): Builder => $query->whereNull('audio_file_path'),
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('create_quiz')
                    ->label('Create Quiz')
                    ->icon('heroicon-o-puzzle-piece')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'ready')
                    ->action(function ($record) {
                        // This will redirect to quiz creation
                        return redirect()->route('filament.admin.resources.game-sessions.create', [
                            'lesson_id' => $record->id
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
