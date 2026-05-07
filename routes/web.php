use App\Http\Controllers\CursoController;

// Generar código de invitación (profesor)
Route::post('/cursos/{id}/codigo-invitacion', [CursoController::class, 'generarCodigoInvitacion']);
// Unirse a curso por código (estudiante)
Route::post('/cursos/unirse', [CursoController::class, 'unirsePorCodigo']);
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
