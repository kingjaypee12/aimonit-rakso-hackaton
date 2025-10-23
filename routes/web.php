<?php

use App\Livewire\Student\QuestionnairesIntroSheets;
use App\Livewire\Student\QuestionnairesSheets;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('quiz')->group(function () {
    Route::get('game/{game_pin}/{participant_id}', QuestionnairesSheets::class)->name('student.quiz_sheet');
    Route::get('participant', QuestionnairesIntroSheets::class)->name('participant');
});
