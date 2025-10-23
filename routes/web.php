<?php

use App\Http\Controllers\LessonController;
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
});
