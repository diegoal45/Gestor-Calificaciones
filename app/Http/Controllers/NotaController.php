<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Tarea;
use App\Models\Curso;
use App\Models\EvaluacionRubrica;
use App\Http\Requests\NotaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function contextoCalificacion(int $tareaId)
    {
        $tarea = Tarea::with(['curso.inscripciones.estudiante', 'rubricas.criterios.niveles'])->findOrFail($tareaId);

        $estudiantes = $tarea->curso->inscripciones->map(function ($inscripcion) {
            return [
                'id' => $inscripcion->estudiante->id,
                'nombre' => $inscripcion->estudiante->nombre,
                'email' => $inscripcion->estudiante->email,
            ];
        })->values();

        $notas = Nota::with(['evaluacionesRubrica'])
            ->where('id_tarea', $tareaId)
            ->get()
            ->keyBy('id_estudiante');

        return response()->json([
            'tarea' => $tarea,
            'estudiantes' => $estudiantes,
            'notas' => $notas,
        ]);
    }

    public function calificar(Request $request, int $tareaId, int $estudianteId)
    {
        $tarea = Tarea::with('rubricas.criterios.niveles')->findOrFail($tareaId);

        $rules = [
            'feedback' => 'required|string|min:3',
            'nota' => 'nullable|numeric|min:0|max:5',
            'rubrica_id' => 'nullable|exists:rubricas,id',
            'evaluaciones' => 'nullable|array',
            'evaluaciones.*.id_criterio' => 'required_with:evaluaciones|exists:criterios,id',
            'evaluaciones.*.id_nivel' => 'required_with:evaluaciones|exists:niveles_criterio,id',
            'evaluaciones.*.porcentaje' => 'required_with:evaluaciones|numeric|min:0|max:100',
        ];
        $data = $request->validate($rules);

        $notaValor = $data['nota'] ?? null;
        $evaluacionesPayload = collect($data['evaluaciones'] ?? []);

        if ($tarea->tipo === 'rubrica') {
            if (empty($data['rubrica_id'])) {
                return response()->json(['message' => 'Debes seleccionar una rúbrica para calificar esta tarea.'], 422);
            }

            $rubrica = $tarea->rubricas->firstWhere('id', (int) $data['rubrica_id']);
            if (!$rubrica) {
                return response()->json(['message' => 'La rúbrica seleccionada no pertenece a la tarea.'], 422);
            }

            $criterios = $rubrica->criterios;
            if ($evaluacionesPayload->count() !== $criterios->count()) {
                return response()->json(['message' => 'Debes evaluar todos los criterios de la rúbrica.'], 422);
            }

            $pesoTotal = (float) $criterios->sum('peso');
            if ($pesoTotal <= 0) {
                return response()->json(['message' => 'La rúbrica no tiene pesos válidos.'], 422);
            }

            $criteriosPorId = $criterios->keyBy('id');
            // El % logrado por criterio se define al calificar (no viene fijo en la rúbrica)
            $sumaPonderadaPct = 0;
            foreach ($evaluacionesPayload as $evaluacion) {
                $criterioId = (int) $evaluacion['id_criterio'];
                $nivelId = (int) $evaluacion['id_nivel'];
                $logroPct = (float) $evaluacion['porcentaje']; // 0..100
                $criterio = $criteriosPorId->get($criterioId);
                if (!$criterio) {
                    return response()->json(['message' => 'Hay criterios inválidos en la evaluación.'], 422);
                }

                $nivel = $criterio->niveles->firstWhere('id', $nivelId);
                if (!$nivel) {
                    return response()->json(['message' => 'Hay niveles inválidos para el criterio seleccionado.'], 422);
                }

                $sumaPonderadaPct += $logroPct * ((float) $criterio->peso / 100);
            }

            $pctFinal = $sumaPonderadaPct / ($pesoTotal / 100); // 0..100
            // Nota final en escala 0..5 (compatibilidad con frontend)
            $notaValor = round(($pctFinal / 100) * 5, 2);
        }

        if ($notaValor === null) {
            return response()->json(['message' => 'Debes ingresar una nota válida para calificación manual.'], 422);
        }

        $nota = DB::transaction(function () use ($tareaId, $estudianteId, $data, $notaValor, $evaluacionesPayload) {
            $nota = Nota::updateOrCreate(
                ['id_tarea' => $tareaId, 'id_estudiante' => $estudianteId],
                ['nota' => $notaValor, 'feedback' => $data['feedback']]
            );

            EvaluacionRubrica::where('id_nota', $nota->id)->delete();
            foreach ($evaluacionesPayload as $evaluacion) {
                EvaluacionRubrica::create([
                    'id_nota' => $nota->id,
                    'id_criterio' => $evaluacion['id_criterio'],
                    'id_nivel' => $evaluacion['id_nivel'],
                    'porcentaje' => $evaluacion['porcentaje'] ?? null,
                ]);
            }

            return $nota->fresh(['evaluacionesRubrica.criterio', 'evaluacionesRubrica.nivel']);
        });

        return response()->json([
            'message' => 'Calificación guardada',
            'nota' => $nota,
        ]);
    }
}
