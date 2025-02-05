<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MusicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/**
 * Testes
 */
Route::post("/backend-response", function () {
    return 'Ok!';
});


Route::get('/mysql-response', function () {
    try {
        \DB::connection()->getPdo();
        return 'Ok!';
    } catch (\Exception $e) {
        return 'Erro: ' . $e->getMessage();
    }
});

/**
 * Autenticacao
 */
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

/**
 * Relacionamento das musicas
 */

Route::get('/musics', [MusicController::class, 'getAllMusics']);
Route::get('/user/{user_id}/musics', [MusicController::class, 'getUserMusics']);
Route::get('/users-with-musics', [MusicController::class, 'getUsersWithMusics']);

Route::post('/music/{music_id}/user/{user_id}', [MusicController::class, 'associateMusicToUser'])
    ->middleware(['auth:sanctum', 'validate.user.action']);

Route::delete('/music/{music_id}/user/{user_id}', [MusicController::class, 'dissociateMusicToUser'])
    ->middleware(['auth:sanctum', 'validate.user.action']);
