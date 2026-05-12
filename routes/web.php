<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas para notificaciones (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::post('/notifications/test', [NotificationController::class, 'probarNotificaciones']);
    Route::post('/notifications/welcome', [NotificationController::class, 'enviarBienvenidaManual']);
    Route::get('/notifications/config', [NotificationController::class, 'verificarConfiguracionEmail']);
});
