<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Resources\Lessons\LessonResource;
use App\Models\Questionnaire;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Notifications\Action as NotificationsAction;
use Illuminate\Support\Facades\Http;

class ViewLesson extends ViewRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            
            Action::make('createGameSession')
                ->label('Create Game Session')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn () => $this->record->status === 'ready')
                ->requiresConfirmation()
                ->modalHeading('Create Game Session')
                ->modalDescription('This will create a new interactive quiz session for students to join using a unique PIN code.')
                ->modalSubmitActionLabel('Create Session')
                ->action(function () {
                    try {
                        // Check if questionnaire exists
                        $questionnaire = Questionnaire::where('code', 'LIKE', "QUEST_{$this->record->id}_%")
                            ->latest()
                            ->first();

                        if (!$questionnaire) {
                            Notification::make()
                                ->title('No Questionnaire Found')
                                ->body('Please generate questions for this lesson first before creating a game session.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Make HTTP request to create game session
                        $response = Http::post(route('lessons.create-game-session', $this->record->id));
                        
                        if ($response->successful()) {
                            $data = $response->json();
                            
                            Notification::make()
                                ->title('Game Session Created!')
                                ->body("Game PIN: {$data['data']['game_pin']} - Students can now join the quiz!")
                                ->success()
                                ->persistent()
                                ->actions([
                                    NotificationsAction::make('copy_pin')
                                        ->label('Copy PIN')
                                        ->button()
                                        ->action(function () use ($data) {
                                            $this->js("navigator.clipboard.writeText('{$data['data']['game_pin']}')");
                                        }),
                                ])
                                ->send();
                        } else {
                            $error = $response->json();
                            Notification::make()
                                ->title('Failed to Create Game Session')
                                ->body($error['message'] ?? 'An error occurred while creating the game session.')
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body('Failed to create game session. Please try again. ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
