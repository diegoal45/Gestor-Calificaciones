<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    // Listar inscripciones de un curso
    public function index($cursoId)
    {
        $inscripciones = Inscripcion::with('estudiante')
            ->where('id_curso', $cursoId)
            ->get();
        return response()->json($inscripciones);
    }

    // Inscribir estudiante manualmente
    public function store(Request $request, $cursoId)
    {
        $data = $request->validate([
            'id_estudiante' => 'required|exists:usuarios,id',
            'grupo' => 'nullable|string|max:50',
        ]);
        $data['id_curso'] = $cursoId;
        $inscripcion = Inscripcion::create($data);
        return response()->json($inscripcion, 201);
    }

    // Importar inscripciones desde archivo Excel (estructura flexible)
    public function import(Request $request, $cursoId)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,csv',
        ]);
        // Aquí deberías usar una librería como Maatwebsite\Excel para procesar el archivo
        // Por ahora, solo simula la importación
        // $imported = ...
        return response()->json(['message' => 'Importación simulada. Implementar lógica real.']);
    }

    // Eliminar inscripción
    public function destroy($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion->delete();
        return response()->json(['message' => 'Inscripción eliminada']);
    }

    // Exportar inscripciones de un curso
    public function export($cursoId)
    {
        $inscripciones = Inscripcion::with('estudiante')
            ->where('id_curso', $cursoId)
            ->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($inscripciones);
    }
}
