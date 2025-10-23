<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\GameSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Lesson management routes
Route::middleware(['auth', 'web'])->group(function () {
    // Audio upload route for lesson recording
    Route::post('/admin/lessons/upload-recording', [LessonController::class, 'uploadRecording'])
        ->name('lessons.upload-recording');

    // Process lesson for transcription and quiz generation
    Route::post('/admin/lessons/process', [LessonController::class, 'processLesson'])
        ->name('lessons.process');

    // Audio file download and streaming
    Route::get('/admin/lessons/{lesson}/download-audio', [LessonController::class, 'downloadAudio'])
        ->name('lessons.download-audio');

    Route::get('/admin/lessons/{lesson}/stream-audio', [LessonController::class, 'streamAudio'])
        ->name('lessons.stream-audio');

    // Game session routes
    Route::post('/admin/lessons/{lesson}/create-game-session', [GameSessionController::class, 'createSession'])
        ->name('lessons.create-game-session');
    
    Route::get('/admin/game-sessions/{gameSession}', [GameSessionController::class, 'getSession'])
        ->name('game-sessions.show');
});

// Questionnaire routes
Route::prefix('api')->group(function () {
    // POST endpoint for N8N to submit questionnaire data
    Route::post('/questionnaires', [QuestionnaireController::class, 'store'])
        ->name('questionnaires.store');
    
    // GET endpoints for frontend consumption
    Route::get('/questionnaires/{code}', [QuestionnaireController::class, 'show'])
        ->name('questionnaires.show');
    
    Route::get('/questionnaires/lesson/{lessonId}', [QuestionnaireController::class, 'getByLessonId'])
        ->name('questionnaires.by-lesson');
});
