<?php

use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('game')->group(function () {
    Route::post('/answer', [GameController::class, 'storeAnswer'])->name('api.game.store-answer');

    Route::get('/session/{gameSessionId}/participant/{participantId}/answers', [GameController::class, 'getParticipantAnswers'])
        ->name('api.game.participant-answers');

    Route::get('/session/{gameSessionId}/leaderboard', [GameController::class, 'getLeaderboard'])
        ->name('api.game.leaderboard');
});
