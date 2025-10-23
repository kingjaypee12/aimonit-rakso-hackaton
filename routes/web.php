<?php

use App\Livewire\Student\QuestionnairesSheets;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('student')->group(function () {
    Route::get('quiz/{game_pin}', QuestionnairesSheets::class)->name('student.quiz_sheet');
});
