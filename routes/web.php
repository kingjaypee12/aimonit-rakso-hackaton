<?php

use App\Http\Controllers\Student\ResultController;
use App\Livewire\Student\Leaderboards;
use App\Livewire\Student\QuestionnairesIntroSheets;
use App\Livewire\Student\QuestionnairesSheets;
use Illuminate\Support\Facades\Route;

Route::get('/', QuestionnairesIntroSheets::class);


Route::prefix('quiz')->group(function () {
    Route::get('game/{game_pin}/{participant_id}', QuestionnairesSheets::class)->name('student.quiz_sheet');
    Route::get('participant', QuestionnairesIntroSheets::class)->name('participant');
    Route::get('leaderboards/{game_pin}/{participant_id?}', Leaderboards::class)->name('leaderboards');
    Route::get('result-record/{game_pin}/{participant_id?}', [ResultController::class, 'index'])->name('result');
});
