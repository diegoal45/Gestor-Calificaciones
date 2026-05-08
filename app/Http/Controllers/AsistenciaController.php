<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Usuario;
use App\Http\Requests\AsistenciaRequest;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    // Listar asistencias de un curso (opcionalmente por fecha)
    public function index(Request $request, $cursoId)
    {
        $query = Asistencia::with('estudiante')->where('id_curso', $cursoId);

        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $asistencias = $query->get();
        return response()->json($asistencias);
    }

    // Listar asistencias de un estudiante en un curso
    public function estudiante($cursoId, $estudianteId)
    {
        $asistencias = Asistencia::where('id_curso', $cursoId)
            ->where('id_estudiante', $estudianteId)
            ->get();
        return response()->json($asistencias);
    }

    // Registrar asistencias en lote para una fecha
    public function store(Request $request, $cursoId)
    {
        $request->validate([
            'fecha' => 'required|date',
            'asistencias' => 'required|array',
            'asistencias.*.id_estudiante' => 'required|exists:usuarios,id',
            'asistencias.*.estado' => 'required|in:P,F,E',
        ]);

        $fecha = $request->fecha;

        foreach ($request->asistencias as $asistenciaData) {
            // Mapear P a presente, F y E a ausente
            $estadoDB = $asistenciaData['estado'] === 'P' ? 'presente' : 'ausente';

            Asistencia::updateOrCreate(
                [
                    'id_curso' => $cursoId,
                    'id_estudiante' => $asistenciaData['id_estudiante'],
                    'fecha' => $fecha
                ],
                [
                    'estado' => $estadoDB
                ]
            );
        }

        return response()->json(['message' => 'Asistencia guardada correctamente'], 201);
    }

    // Actualizar una asistencia existente
    public function update(AsistenciaRequest $request, $id)
    {
        $asistencia = Asistencia::findOrFail($id);
        $asistencia->update($request->validated());
        return response()->json($asistencia);
    }

    // Eliminar una asistencia
    public function destroy($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        $asistencia->delete();
        return response()->json(['message' => 'Asistencia eliminada']);
    }

    // Exportar asistencias de un curso
    public function export($cursoId)
    {
        $asistencias = Asistencia::with('estudiante')
            ->where('id_curso', $cursoId)
            ->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($asistencias);
    }
}
