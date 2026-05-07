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
    /**
     * Lista notas de una tarea.
     * @param int $tareaId
     */
    public function index(int $tareaId)
    {
        $notas = Nota::with('estudiante')->where('id_tarea', $tareaId)->get();
        return response()->json($notas);
    }

    // Mostrar una nota específica con feedback e historial
    /**
     * Muestra una nota específica.
     * @param int $id
     */
    public function show(int $id)
    {
        $nota = Nota::with(['estudiante', 'historialNotas', 'evaluacionesRubrica'])->findOrFail($id);
        return response()->json($nota);
    }

    // Registrar una nueva nota
    /**
     * Registra una nueva nota.
     * @param NotaRequest $request
     * @param int $tareaId
     */
    public function store(NotaRequest $request, int $tareaId)
    {
        $data = $request->validated();
        $data['id_tarea'] = $tareaId;
        $nota = Nota::create($data);
        return response()->json($nota, 201);
    }

    // Actualizar una nota existente
    /**
     * Actualiza una nota existente.
     * @param NotaRequest $request
     * @param int $id
     */
    public function update(NotaRequest $request, int $id)
    {
        $nota = Nota::findOrFail($id);
        $nota->update($request->validated());
        return response()->json($nota);
    }

    // Eliminar una nota
    /**
     * Elimina una nota.
     * @param int $id
     */
    public function destroy(int $id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();
        return response()->json(['message' => 'Nota eliminada']);
    }

    // Registrar feedback para una nota
    /**
     * Registra feedback para una nota.
     * @param Request $request
     * @param int $id
     */
    public function feedback(Request $request, int $id)
    {
        $nota = Nota::findOrFail($id);
        $request->validate(['feedback' => 'required|string']);
        $nota->feedback = $request->feedback;
        $nota->save();
        return response()->json(['message' => 'Feedback registrado', 'nota' => $nota]);
    }

    // Calcular promedio y nota final de un estudiante en un curso
    /**
     * Calcula promedio y nota final de un estudiante en un curso.
     * @param int $cursoId
     * @param int $estudianteId
     */
    public function promedio(int $cursoId, int $estudianteId)
    {
        $tareas = Tarea::where('id_curso', $cursoId)->get();
        $notas = Nota::whereIn('id_tarea', $tareas->pluck('id'))
            ->where('id_estudiante', $estudianteId)
            ->get();
        $promedio = $notas->avg('nota');
        return response()->json(['promedio' => $promedio]);
    }

    // Simulador de notas: calcula nota final con posibles valores
    /**
     * Simulador de notas.
     * @param Request $request
     * @param int $cursoId
     * @param int $estudianteId
     */
    public function simular(Request $request, int $cursoId, int $estudianteId)
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
    /**
     * Exporta notas de una tarea.
     * @param int $tareaId
     */
    public function export(int $tareaId)
    {
        $notas = Nota::with('estudiante')->where('id_tarea', $tareaId)->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($notas);
    }
}
