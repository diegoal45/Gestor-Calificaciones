<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CriterioController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\NivelCriterioController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\ReclamoController;
use App\Http\Controllers\RubricaController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\EstudianteController;

// Rutas públicas (sin autenticación)
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {
	// Logout (si implementas tokens)
	Route::post('logout', [AuthController::class, 'logout'])->name('logout');
	Route::get('me', [AuthController::class, 'me']);
	Route::put('me', [AuthController::class, 'updateProfile']);
	Route::put('me/password', [AuthController::class, 'updatePassword']);

	// Asistencia
	Route::get('asistencias/curso/{cursoId}', [AsistenciaController::class, 'index']);
	Route::get('asistencias/curso/{cursoId}/estudiante/{estudianteId}', [AsistenciaController::class, 'estudiante']);
	Route::post('asistencias/curso/{cursoId}', [AsistenciaController::class, 'store']);
	Route::put('asistencias/{id}', [AsistenciaController::class, 'update']);
	Route::delete('asistencias/{id}', [AsistenciaController::class, 'destroy']);
	Route::get('asistencias/export/curso/{cursoId}', [AsistenciaController::class, 'export']);

	// Cursos
	Route::get('cursos', [CursoController::class, 'index']);
	Route::get('cursos/{id}', [CursoController::class, 'show']);
	Route::post('cursos', [CursoController::class, 'store']);
	Route::put('cursos/{id}', [CursoController::class, 'update']);
	Route::delete('cursos/{id}', [CursoController::class, 'destroy']);
	Route::post('cursos/{id}/generar-codigo', [CursoController::class, 'generarCodigoInvitacion']);
	Route::post('cursos/{id}/asistencia-config', [CursoController::class, 'actualizarAsistenciaConfig']);
	Route::post('cursos/unirse', [CursoController::class, 'unirsePorCodigo']);
	Route::get('cursos/export/{id}', [CursoController::class, 'export']);
	
	// Cursos - Vistas Profesor
	Route::get('cursos/{id}/resumen', [CursoController::class, 'resumen']);
	Route::get('cursos/{id}/planilla', [CursoController::class, 'planilla']);
	Route::get('cursos/{id}/analisis', [CursoController::class, 'analisis']);
	Route::post('cursos/{id}/calificaciones', [CursoController::class, 'guardarCalificacion']);
	Route::get('cursos/{id}/estudiantes', [CursoController::class, 'estudiantes']);

	// Estudiantes
	Route::get('estudiantes/{id}/perfil', [EstudianteController::class, 'perfil']);
	Route::get('estudiantes/{id}/analisis', [EstudianteController::class, 'analisis']);

	// Inscripciones
	Route::get('inscripciones/curso/{cursoId}', [InscripcionController::class, 'index']);
	Route::post('inscripciones/curso/{cursoId}', [InscripcionController::class, 'store']);
	Route::post('inscripciones/import/curso/{cursoId}', [InscripcionController::class, 'import']);
	Route::delete('inscripciones/{id}', [InscripcionController::class, 'destroy']);
	Route::get('inscripciones/export/curso/{cursoId}', [InscripcionController::class, 'export']);

	// Tareas
	Route::get('tareas/curso/{cursoId}', [TareaController::class, 'index']);
	Route::get('tareas/{id}', [TareaController::class, 'show']);
	Route::post('tareas/curso/{cursoId}', [TareaController::class, 'store']);
	Route::put('tareas/{id}', [TareaController::class, 'update']);
	Route::delete('tareas/{id}', [TareaController::class, 'destroy']);
	Route::get('tareas/export/curso/{cursoId}', [TareaController::class, 'export']);

	// Notas
	Route::get('notas/tarea/{tareaId}', [NotaController::class, 'index']);
	Route::get('notas/{id}', [NotaController::class, 'show']);
	Route::post('notas/tarea/{tareaId}', [NotaController::class, 'store']);
	Route::put('notas/{id}', [NotaController::class, 'update']);
	Route::delete('notas/{id}', [NotaController::class, 'destroy']);
	Route::post('notas/{id}/feedback', [NotaController::class, 'feedback']);
	Route::get('notas/tarea/{tareaId}/contexto-calificacion', [NotaController::class, 'contextoCalificacion']);
	Route::post('notas/tarea/{tareaId}/estudiante/{estudianteId}/calificar', [NotaController::class, 'calificar']);
	Route::get('notas/promedio/{cursoId}/{estudianteId}', [NotaController::class, 'promedio']);
	Route::post('notas/simular/{cursoId}/{estudianteId}', [NotaController::class, 'simular']);
	Route::get('notas/export/tarea/{tareaId}', [NotaController::class, 'export']);

	// Reclamos
	Route::get('reclamos/curso/{cursoId}', [ReclamoController::class, 'indexCurso']);
	Route::get('reclamos/estudiante/{estudianteId}', [ReclamoController::class, 'indexEstudiante']);
	Route::get('reclamos/nota/{notaId}', [ReclamoController::class, 'indexNota']);
	Route::post('reclamos/nota/{notaId}', [ReclamoController::class, 'store']);
	Route::post('reclamos/{id}/responder', [ReclamoController::class, 'responder']);
	Route::post('reclamos/{id}/cerrar', [ReclamoController::class, 'cerrar']);
	Route::delete('reclamos/{id}', [ReclamoController::class, 'destroy']);
	Route::get('reclamos/export/estudiante/{estudianteId}', [ReclamoController::class, 'export']);

	// Rúbricas
	Route::get('rubricas/tarea/{tareaId}', [RubricaController::class, 'index']);
	Route::get('rubricas/{id}', [RubricaController::class, 'show']);
	Route::post('rubricas/tarea/{tareaId}', [RubricaController::class, 'store']);
	Route::put('rubricas/{id}', [RubricaController::class, 'update']);
	Route::delete('rubricas/{id}', [RubricaController::class, 'destroy']);
	Route::get('rubricas/export/tarea/{tareaId}', [RubricaController::class, 'export']);

	// Criterios
	Route::get('criterios/rubrica/{rubricaId}', [CriterioController::class, 'index']);
	Route::get('criterios/{id}', [CriterioController::class, 'show']);
	Route::post('criterios/rubrica/{rubricaId}', [CriterioController::class, 'store']);
	Route::put('criterios/{id}', [CriterioController::class, 'update']);
	Route::delete('criterios/{id}', [CriterioController::class, 'destroy']);
	Route::get('criterios/export/rubrica/{rubricaId}', [CriterioController::class, 'export']);

	// Niveles de criterio
	Route::get('niveles-criterio/criterio/{criterioId}', [NivelCriterioController::class, 'index']);
	Route::get('niveles-criterio/{id}', [NivelCriterioController::class, 'show']);
	Route::post('niveles-criterio/criterio/{criterioId}', [NivelCriterioController::class, 'store']);
	Route::put('niveles-criterio/{id}', [NivelCriterioController::class, 'update']);
	Route::delete('niveles-criterio/{id}', [NivelCriterioController::class, 'destroy']);
	Route::get('niveles-criterio/export/criterio/{criterioId}', [NivelCriterioController::class, 'export']);
});
