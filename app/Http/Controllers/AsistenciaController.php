<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Usuario;
use App\Http\Requests\AsistenciaRequest;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    // Listar asistencias de un curso
    public function index($cursoId)
    {
        $asistencias = Asistencia::with('estudiante')
            ->where('id_curso', $cursoId)
            ->get();
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

    // Registrar una nueva asistencia
    public function store(AsistenciaRequest $request, $cursoId)
    {
        $data = $request->validated();
        $data['id_curso'] = $cursoId;
        $asistencia = Asistencia::create($data);
        return response()->json($asistencia, 201);
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
