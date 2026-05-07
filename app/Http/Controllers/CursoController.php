    // Generar código de invitación para un curso
    public function generarCodigoInvitacion($id)
    {
        $curso = Curso::findOrFail($id);
        // Generar código aleatorio de 8 caracteres
        $codigo = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);
        $curso->codigo_invitacion = $codigo;
        $curso->save();
        return response()->json(['codigo_invitacion' => $codigo]);
    }

    // Unirse a un curso usando código de invitación
    public function unirsePorCodigo(Request $request)
    {
        $data = $request->validate([
            'codigo_invitacion' => 'required|string',
            'id_estudiante' => 'required|exists:usuarios,id',
        ]);
        $curso = Curso::where('codigo_invitacion', $data['codigo_invitacion'])->first();
        if (!$curso) {
            return response()->json(['error' => 'Código inválido'], 404);
        }
        // Verificar si ya está inscrito
        if ($curso->inscripciones()->where('id_estudiante', $data['id_estudiante'])->exists()) {
            return response()->json(['error' => 'Ya inscrito'], 409);
        }
        $inscripcion = $curso->inscripciones()->create([
            'id_estudiante' => $data['id_estudiante'],
        ]);
        return response()->json(['inscripcion' => $inscripcion], 201);
    }
<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Http\Requests\CursoRequest;
use Illuminate\Http\Request;

class CursoController extends Controller
{

    // Listar todos los cursos con profesor y tareas
    public function index()
    {
        $cursos = Curso::with(['profesor', 'tareas', 'inscripciones.estudiante'])->get();
        return response()->json($cursos);
    }


    // Mostrar un curso específico con detalles y resumen de rendimiento
    public function show($id)
    {
        $curso = Curso::with([
            'profesor',
            'tareas.notas',
            'inscripciones.estudiante',
            'asistencias',
        ])->findOrFail($id);

        // Resumen de rendimiento (promedio general, cantidad de aprobados, reprobados)
        $notas = $curso->tareas->flatMap(function ($tarea) {
            return $tarea->notas;
        });
        $promedio = $notas->avg('nota');
        $aprobados = $notas->where('nota', '>=', $curso->nota_minima_aprobatoria)->count();
        $reprobados = $notas->where('nota', '<', $curso->nota_minima_aprobatoria)->count();

        $resumen = [
            'promedio_general' => $promedio,
            'aprobados' => $aprobados,
            'reprobados' => $reprobados,
        ];

        return response()->json([
            'curso' => $curso,
            'resumen' => $resumen,
        ]);
    }


    // Crear un nuevo curso (asignando profesor y configuración de asistencia)
    public function store(CursoRequest $request)
    {
        $data = $request->validated();
        $curso = Curso::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? '',
            'id_profesor' => $request->user()->id ?? $data['id_profesor'] ?? null,
            'nota_minima_aprobatoria' => $data['nota_minima'],
            'nota_maxima' => $data['nota_maxima'],
            'usa_asistencia' => $data['usa_asistencia'] ?? false,
            'peso_asistencia' => $data['peso_asistencia'] ?? null,
        ]);
        return response()->json($curso, 201);
    }


    // Actualizar un curso existente (incluyendo configuración de asistencia)
    public function update(CursoRequest $request, $id)
    {
        $curso = Curso::findOrFail($id);
        $data = $request->validated();
        $curso->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? '',
            'nota_minima_aprobatoria' => $data['nota_minima'],
            'nota_maxima' => $data['nota_maxima'],
            'usa_asistencia' => $data['usa_asistencia'] ?? $curso->usa_asistencia,
            'peso_asistencia' => $data['peso_asistencia'] ?? $curso->peso_asistencia,
        ]);
        return response()->json($curso);
    }


    // Eliminar un curso
    public function destroy($id)
    {
        $curso = Curso::findOrFail($id);
        $curso->delete();
        return response()->json(['message' => 'Curso eliminado']);
    }

    // Exportar datos del curso (ejemplo: inscripciones y notas)
    public function export($id)
    {
        $curso = Curso::with(['inscripciones.estudiante', 'tareas.notas'])->findOrFail($id);
        // Aquí puedes implementar la lógica de exportación (CSV, Excel, etc.)
        // Por ahora, solo retorna los datos en JSON
        return response()->json($curso);
    }
}
