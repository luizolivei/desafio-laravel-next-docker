<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');
