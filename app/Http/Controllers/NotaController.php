<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Tarea;
use App\Models\Curso;
use App\Http\Requests\NotaRequest;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    // Listar notas de una tarea
    public function index($tareaId)
    {
        $notas = Nota::with('estudiante')->where('id_tarea', $tareaId)->get();
        return response()->json($notas);
    }

    // Mostrar una nota específica con feedback e historial
    public function show($id)
    {
        $nota = Nota::with(['estudiante', 'historialNotas', 'evaluacionesRubrica'])->findOrFail($id);
        return response()->json($nota);
    }

    // Registrar una nueva nota
    public function store(NotaRequest $request, $tareaId)
    {
        $data = $request->validated();
        $data['id_tarea'] = $tareaId;
        $nota = Nota::create($data);
        return response()->json($nota, 201);
    }

    // Actualizar una nota existente
    public function update(NotaRequest $request, $id)
    {
        $nota = Nota::findOrFail($id);
        $nota->update($request->validated());
        return response()->json($nota);
    }

    // Eliminar una nota
    public function destroy($id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();
        return response()->json(['message' => 'Nota eliminada']);
    }

    // Registrar feedback para una nota
    public function feedback(Request $request, $id)
    {
        $nota = Nota::findOrFail($id);
        $request->validate(['feedback' => 'required|string']);
        $nota->feedback = $request->feedback;
        $nota->save();
        return response()->json(['message' => 'Feedback registrado', 'nota' => $nota]);
    }

    // Calcular promedio y nota final de un estudiante en un curso
    public function promedio($cursoId, $estudianteId)
    {
        $tareas = Tarea::where('id_curso', $cursoId)->get();
        $notas = Nota::whereIn('id_tarea', $tareas->pluck('id'))
            ->where('id_estudiante', $estudianteId)
            ->get();
        $promedio = $notas->avg('nota');
        return response()->json(['promedio' => $promedio]);
    }

    // Simulador de notas: calcula nota final con posibles valores
    public function simular(Request $request, $cursoId, $estudianteId)
    {
        $request->validate([
            'notas_simuladas' => 'required|array',
        ]);
        $tareas = Tarea::where('id_curso', $cursoId)->get();
        $notas = Nota::whereIn('id_tarea', $tareas->pluck('id'))
            ->where('id_estudiante', $estudianteId)
            ->get();
        $simuladas = collect($request->notas_simuladas);
        $todas = $notas->pluck('nota')->merge($simuladas);
        $promedio = $todas->avg();
        return response()->json(['nota_final_simulada' => $promedio]);
    }

    // Exportar notas de una tarea
    public function export($tareaId)
    {
        $notas = Nota::with('estudiante')->where('id_tarea', $tareaId)->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($notas);
    }
}
