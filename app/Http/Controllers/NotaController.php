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
    private function calcularPromedioCursoParaEstudiante(Curso $curso, int $estudianteId): float
    {
        $metodo = $curso->metodo_calificacion ?: Curso::METODO_PONDERACION;

        $sumaPonderada = 0.0;
        $totalPorcentaje = 0.0;
        $sumaSimple = 0.0;
        $cantidad = 0;

        foreach ($curso->tareas as $tarea) {
            $nota = $tarea->notas->where('id_estudiante', $estudianteId)->first();
            if (!$nota) continue;

            $valor = (float) $nota->nota;
            if ($metodo === Curso::METODO_PROMEDIO) {
                $sumaSimple += $valor;
                $cantidad++;
                continue;
            }

            $p = (float) ($tarea->porcentaje ?? 0);
            if ($p > 0) {
                $sumaPonderada += $valor * ($p / 100);
                $totalPorcentaje += $p;
            } else {
                $sumaSimple += $valor;
                $cantidad++;
            }
        }

        if ($metodo === Curso::METODO_PROMEDIO) {
            return $cantidad > 0 ? ($sumaSimple / $cantidad) : 0.0;
        }

        if ($totalPorcentaje > 0) {
            return $sumaPonderada / ($totalPorcentaje / 100);
        }

        return $cantidad > 0 ? ($sumaSimple / $cantidad) : 0.0;
    }

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
        $curso = Curso::with(['tareas.notas'])->findOrFail($cursoId);
        $promedio = $this->calcularPromedioCursoParaEstudiante($curso, $estudianteId);
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
        $curso = Curso::with(['tareas.notas'])->findOrFail($cursoId);

        $payload = collect($request->notas_simuladas);
        $isObjectPayload = $payload->first() !== null && is_array($payload->first());

        if (!$isObjectPayload) {
            // Backward compatible: si solo llegan números, simulamos promedio simple.
            $tareas = Tarea::where('id_curso', $cursoId)->get();
            $notas = Nota::whereIn('id_tarea', $tareas->pluck('id'))
                ->where('id_estudiante', $estudianteId)
                ->get();
            $todas = $notas->pluck('nota')->merge($payload);
            return response()->json(['nota_final_simulada' => $todas->avg()]);
        }

        // Nuevo formato: [{ tarea_id, nota }]
        $override = $payload
            ->filter(fn ($x) => isset($x['tarea_id']) && isset($x['nota']))
            ->mapWithKeys(fn ($x) => [(int) $x['tarea_id'] => (float) $x['nota']])
            ->all();

        foreach ($curso->tareas as $tarea) {
            if (array_key_exists($tarea->id, $override)) {
                $nota = $tarea->notas->where('id_estudiante', $estudianteId)->first();
                if ($nota) {
                    $nota->nota = $override[$tarea->id];
                } else {
                    $tarea->setRelation('notas', $tarea->notas->push(new Nota([
                        'id_estudiante' => $estudianteId,
                        'id_tarea' => $tarea->id,
                        'nota' => $override[$tarea->id],
                    ])));
                }
            }
        }

        $promedio = $this->calcularPromedioCursoParaEstudiante($curso, $estudianteId);
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
            'evaluaciones.*.porcentaje' => 'nullable|numeric|min:0|max:100',
        ];
        $data = $request->validate($rules);

        $notaValor = $data['nota'] ?? null;
        $evaluacionesPayload = collect($data['evaluaciones'] ?? []);

        if ($tarea->tipo === 'rubrica') {
            // Todos los criterios de todas las rúbricas de la tarea (una sola calificación cubre la tarea completa)
            $criterios = $tarea->rubricas->flatMap(fn ($r) => $r->criterios)->unique('id')->values();
            if ($criterios->isEmpty()) {
                return response()->json(['message' => 'Esta tarea no tiene criterios de rúbrica configurados.'], 422);
            }

            if ($evaluacionesPayload->pluck('id_criterio')->unique()->count() !== $evaluacionesPayload->count()) {
                return response()->json(['message' => 'Hay criterios repetidos en la evaluación enviada.'], 422);
            }

            if ($evaluacionesPayload->count() !== $criterios->count()) {
                return response()->json(['message' => 'Debes elegir un nivel en cada criterio de todas las rúbricas de esta tarea.'], 422);
            }

            $pesoTotal = (float) $criterios->sum('peso');
            if ($pesoTotal <= 0) {
                return response()->json(['message' => 'Los criterios no tienen pesos válidos (suma debe ser mayor que 0).'], 422);
            }

            $criteriosPorId = $criterios->keyBy('id');
            $sumaPonderadaPct = 0;
            $evaluacionesPayload = $evaluacionesPayload->map(function ($evaluacion) use ($criteriosPorId) {
                $criterioId = (int) $evaluacion['id_criterio'];
                $nivelId = (int) $evaluacion['id_nivel'];
                $criterio = $criteriosPorId->get($criterioId);
                if (!$criterio) {
                    throw new \InvalidArgumentException('Hay criterios que no pertenecen a esta tarea.');
                }

                $nivel = $criterio->niveles->firstWhere('id', $nivelId);
                if (!$nivel) {
                    throw new \InvalidArgumentException('Hay niveles inválidos para el criterio seleccionado.');
                }

                // % de logro del criterio según el nivel (0–100); ej. 60 => 60% del peso de ese criterio en la nota
                $porcentaje = array_key_exists('porcentaje', $evaluacion) && $evaluacion['porcentaje'] !== null
                    ? (float) $evaluacion['porcentaje']
                    : (float) $nivel->valor;
                $porcentaje = max(0, min(100, $porcentaje));
                $evaluacion['porcentaje'] = $porcentaje;
                return $evaluacion;
            });

            foreach ($evaluacionesPayload as $evaluacion) {
                $criterio = $criteriosPorId->get((int) $evaluacion['id_criterio']);
                $logroPct = (float) $evaluacion['porcentaje'];
                $sumaPonderadaPct += $logroPct * ((float) $criterio->peso / 100);
            }

            $pctFinal = $sumaPonderadaPct / ($pesoTotal / 100);
            $notaValor = round(($pctFinal / 100) * 5, 2);
        }

        if ($notaValor === null) {
            return response()->json(['message' => 'Debes ingresar una nota válida para calificación manual.'], 422);
        }

        try {
            $nota = DB::transaction(function () use ($tareaId, $estudianteId, $data, $notaValor, $evaluacionesPayload) {
            $nota = Nota::updateOrCreate(
                ['id_tarea' => $tareaId, 'id_estudiante' => $estudianteId],
                ['nota' => $notaValor, 'feedback' => $data['feedback']]
            );

            EvaluacionRubrica::where('id_nota', $nota->id)->delete();
            foreach ($evaluacionesPayload as $evaluacion) {
                EvaluacionRubrica::create([
                    'id_nota' => $nota->id,
                    'id_criterio' => (int) $evaluacion['id_criterio'],
                    'id_nivel' => (int) $evaluacion['id_nivel'],
                    'porcentaje' => array_key_exists('porcentaje', $evaluacion) ? (float) $evaluacion['porcentaje'] : null,
                ]);
            }

            return $nota->fresh(['evaluacionesRubrica.criterio', 'evaluacionesRubrica.nivel']);
            });
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Calificación guardada',
            'nota' => $nota,
        ]);
    }
}
