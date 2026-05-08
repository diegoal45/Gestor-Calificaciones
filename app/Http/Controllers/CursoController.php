<?php
namespace App\Http\Controllers;
use App\Models\Curso;
use App\Models\Nota;
use App\Models\Asistencia;
use App\Http\Requests\CursoRequest;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    // Generar código de invitación para un curso
    /**
     * Genera un código de invitación para un curso.
     * @param int $id
     */
    public function generarCodigoInvitacion(int $id)
    {
        $curso = Curso::findOrFail($id);

        // Generar código único de 8 caracteres (reintentos para evitar colisión)
        $codigo = null;
        for ($intento = 0; $intento < 5; $intento++) {
            $candidato = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);
            $existe = Curso::where('codigo_invitacion', $candidato)
                ->where('id', '!=', $curso->id)
                ->exists();

            if (!$existe) {
                $codigo = $candidato;
                break;
            }
        }

        if (!$codigo) {
            return response()->json(['message' => 'No fue posible generar un codigo unico'], 500);
        }

        $curso->codigo_invitacion = $codigo;
        $curso->save();
        $curso->refresh();

        return response()->json([
            'codigo_invitacion' => $curso->codigo_invitacion,
            'curso_id' => $curso->id,
        ]);
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

    public function index(Request $request)
    {
        $usuario = auth()->user();
        $q = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 6);
        if ($perPage < 1) $perPage = 6;
        if ($perPage > 30) $perPage = 30;

        $baseQuery = Curso::with(['profesor', 'tareas.notas', 'inscripciones.estudiante']);

        if ($usuario->rol === 'profesor') {
            // Solo los cursos donde el usuario es profesor
            $baseQuery->where('id_profesor', $usuario->id);
        } else {
            // Solo los cursos en los que el estudiante está inscrito
            $baseQuery->whereHas('inscripciones', function ($query) use ($usuario) {
                    $query->where('id_estudiante', $usuario->id);
                })
            ;
        }

        if ($q !== '') {
            $baseQuery->where(function ($query) use ($q) {
                $query->where('nombre', 'like', '%' . $q . '%')
                      ->orWhere('descripcion', 'like', '%' . $q . '%');
            });
        }

        $paginator = $baseQuery
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $cursos = $paginator->getCollection();

        // Agregar métricas básicas para el Dashboard
        foreach ($cursos as $curso) {
            $curso->estudiantes_count = $curso->inscripciones->count();
            
            $notas = $curso->tareas->flatMap(function ($tarea) {
                return $tarea->notas;
            });
            $promedio = $notas->avg('nota');
            $curso->promedio_general = $promedio !== null ? round($promedio, 2) : 0;
            
            // Para evitar enviar demasiados datos, podríamos ocultar `tareas` e `inscripciones` si no se necesitan,
            // pero para no romper compatibilidad, las mantendremos.
        }

        return response()->json([
            'data' => $cursos,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }


    // Mostrar un curso específico con detalles y resumen de rendimiento
    /**
     * Muestra un curso específico con detalles y resumen de rendimiento.
     * @param int $id
     */
    public function show(int $id)
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

        $curso->estudiantes_count = $curso->inscripciones->count();
        $curso->promedio_general = $promedio !== null ? round($promedio, 2) : 0;

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
    /**
     * Actualiza un curso existente.
     * @param CursoRequest $request
     * @param int $id
     */
    public function update(CursoRequest $request, int $id)
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
    /**
     * Elimina un curso.
     * @param int $id
     */
    public function destroy(int $id)
    {
        $curso = Curso::findOrFail($id);
        $curso->delete();
        return response()->json(['message' => 'Curso eliminado']);
    }

    // Configurar si la asistencia pesa en la nota final del curso
    public function actualizarAsistenciaConfig(Request $request, int $id)
    {
        $data = $request->validate([
            'usa_asistencia' => 'required|boolean',
            'peso_asistencia' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($data['usa_asistencia'] && ($data['peso_asistencia'] === null)) {
            return response()->json(['message' => 'Debe indicar el porcentaje de asistencia'], 422);
        }

        $curso = Curso::findOrFail($id);
        $curso->usa_asistencia = $data['usa_asistencia'];
        $curso->peso_asistencia = $data['usa_asistencia'] ? $data['peso_asistencia'] : null;
        $curso->save();

        return response()->json([
            'message' => 'Configuracion de asistencia actualizada',
            'curso' => $curso,
        ]);
    }

    // Exportar datos del curso (ejemplo: inscripciones y notas)
    /**
     * Exporta datos del curso.
     * @param int $id
     */
    public function export(int $id)
    {
        $curso = Curso::with(['inscripciones.estudiante', 'tareas.notas'])->findOrFail($id);
        // Aquí puedes implementar la lógica de exportación (CSV, Excel, etc.)
        // Por ahora, solo retorna los datos en JSON
        return response()->json($curso);
    }

    // Dashboard: Resumen del curso
    public function resumen(int $id)
    {
        $curso = Curso::with(['tareas.notas', 'inscripciones.estudiante'])->findOrFail($id);

        $notasTotales = $curso->tareas->flatMap->notas;
        $promedioGeneral = $notasTotales->avg('nota') ?? 0;
        
        $aprobados = 0;
        $riesgo = 0;

        foreach ($curso->inscripciones as $inscripcion) {
            $estudianteId = $inscripcion->id_estudiante;
            
            // Calculate student average
            $sumaNotas = 0;
            $pesoTotal = 0;
            foreach ($curso->tareas as $tarea) {
                $nota = $tarea->notas->where('id_estudiante', $estudianteId)->first();
                if ($nota) {
                    $sumaNotas += $nota->nota * ($tarea->porcentaje / 100);
                    $pesoTotal += $tarea->porcentaje;
                }
            }
            $promedioEstudiante = $pesoTotal > 0 ? ($sumaNotas / ($pesoTotal / 100)) : 0;
            
            if ($promedioEstudiante >= $curso->nota_minima_aprobatoria) {
                $aprobados++;
            } else if ($promedioEstudiante > 0 && $promedioEstudiante < 3.0) { // Riesgo custom threshold
                $riesgo++;
            }
        }

        $chartBuckets = [
            '0-1.9' => 0,
            '2.0-2.9' => 0,
            '3.0-3.9' => 0,
            '4.0-5.0' => 0,
        ];

        foreach ($notasTotales as $nota) {
            $valor = (float) $nota->nota;
            if ($valor < 2.0) {
                $chartBuckets['0-1.9']++;
            } elseif ($valor < 3.0) {
                $chartBuckets['2.0-2.9']++;
            } elseif ($valor < 4.0) {
                $chartBuckets['3.0-3.9']++;
            } else {
                $chartBuckets['4.0-5.0']++;
            }
        }

        $maxBucket = max(1, ...array_values($chartBuckets));
        $colors = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
        $chartData = [];
        $i = 0;
        foreach ($chartBuckets as $label => $count) {
            $chartData[] = [
                'label' => $label,
                'value' => $count,
                'height' => (int) round(($count / $maxBucket) * 100),
                'colorClass' => $colors[$i++] ?? 'bg-secondary',
            ];
        }

        $recentActivity = $curso->tareas
            ->sortByDesc('fecha_limite')
            ->take(5)
            ->values()
            ->map(function ($tarea) {
                return [
                    'title' => 'Tarea: ' . $tarea->nombre,
                    'desc' => 'Fecha limite: ' . $tarea->fecha_limite,
                    'time' => 'Registro del curso',
                    'dotClass' => 'bg-primary',
                ];
            });

        return response()->json([
            'stats' => [
                'promedio' => number_format($promedioGeneral, 1),
                'tendenciaPromedio' => 0,
                'aprobando' => $aprobados,
                'riesgo' => $riesgo,
                'tareasActivas' => $curso->tareas->where('fecha_limite', '>=', now())->count()
            ],
            'chartData' => $chartData,
            'activity' => $recentActivity,
        ]);
    }

    // Dashboard: Planilla completa
    public function planilla(int $id)
    {
        $curso = Curso::with(['tareas', 'inscripciones.estudiante', 'tareas.notas'])->findOrFail($id);
        
        $estudiantes = $curso->inscripciones->map(function ($ins) {
            return [
                'id' => $ins->estudiante->id,
                'nombre' => $ins->estudiante->nombre,
                'email' => $ins->estudiante->email,
            ];
        });

        $calificaciones = [];
        foreach ($curso->tareas as $tarea) {
            foreach ($tarea->notas as $nota) {
                $calificaciones[] = [
                    'estudiante_id' => $nota->id_estudiante,
                    'tarea_id' => $nota->id_tarea,
                    'nota' => $nota->nota
                ];
            }
        }

        $asistenciaRows = Asistencia::where('id_curso', $id)->get();
        $asistenciaStats = [];
        foreach ($curso->inscripciones as $ins) {
            $estudianteId = $ins->estudiante->id;
            $regs = $asistenciaRows->where('id_estudiante', $estudianteId);
            $total = $regs->count();
            $presentes = $regs->where('estado', 'presente')->count();
            $porcentaje = $total > 0 ? round(($presentes / $total) * 100, 2) : 0;
            $asistenciaStats[] = [
                'estudiante_id' => $estudianteId,
                'porcentaje' => $porcentaje,
                'total_registros' => $total,
            ];
        }

        return response()->json([
            'tareas' => $curso->tareas,
            'estudiantes' => $estudiantes,
            'calificaciones' => $calificaciones,
            'asistencia' => $asistenciaStats,
            'curso' => [
                'usa_asistencia' => (bool) $curso->usa_asistencia,
                'peso_asistencia' => $curso->peso_asistencia ? (float) $curso->peso_asistencia : 0,
                'nota_maxima' => (float) $curso->nota_maxima,
            ],
        ]);
    }

    // Dashboard: Guardar Calificación (Upsert)
    public function guardarCalificacion(Request $request, int $id)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:usuarios,id',
            'tarea_id' => 'required|exists:tareas,id',
            'nota' => 'required|numeric|min:0|max:5',
        ]);

        // Verificar que la tarea pertenece al curso
        $curso = Curso::findOrFail($id);
        if (!$curso->tareas()->where('id', $request->tarea_id)->exists()) {
            return response()->json(['message' => 'La tarea no pertenece al curso'], 400);
        }

        $nota = Nota::updateOrCreate(
            ['id_tarea' => $request->tarea_id, 'id_estudiante' => $request->estudiante_id],
            ['nota' => $request->nota]
        );

        return response()->json(['message' => 'Nota guardada', 'nota' => $nota]);
    }

    // Dashboard: Estudiantes con promedios
    public function estudiantes(int $id)
    {
        $curso = Curso::with(['inscripciones.estudiante', 'tareas.notas'])->findOrFail($id);
        
        $estudiantes = $curso->inscripciones->map(function ($ins) use ($curso) {
            $estudianteId = $ins->estudiante->id;
            
            // Calculate student average
            $sumaNotas = 0;
            $pesoTotal = 0;
            foreach ($curso->tareas as $tarea) {
                $nota = $tarea->notas->where('id_estudiante', $estudianteId)->first();
                if ($nota) {
                    $sumaNotas += $nota->nota * ($tarea->porcentaje / 100);
                    $pesoTotal += $tarea->porcentaje;
                }
            }
            $promedio = $pesoTotal > 0 ? ($sumaNotas / ($pesoTotal / 100)) : 0;
            
            $estado = 'Normal';
            $riesgo = 'Bajo';
            
            if ($promedio >= 4.0) { $estado = 'Excelente'; }
            else if ($promedio >= $curso->nota_minima_aprobatoria) { $estado = 'Bueno'; }
            else if ($promedio > 0 && $promedio < 3.0) { $estado = 'Crítico'; $riesgo = 'Alto'; }
            else if ($promedio == 0) { $estado = 'Sin notas'; }

            return [
                'id' => $estudianteId,
                'nombre' => $ins->estudiante->nombre,
                'email' => $ins->estudiante->email,
                'grupo' => 'A1', // Mock for now
                'promedio' => $promedio > 0 ? number_format($promedio, 1) : '-',
                'estado' => $estado,
                'riesgo' => $riesgo
            ];
        });

        return response()->json($estudiantes);
    }
    // Dashboard: Análisis detallado del curso
    public function analisis(int $id)
    {
        $curso = Curso::with(['tareas.notas', 'inscripciones.estudiante', 'asistencias'])->findOrFail($id);

        $riesgoCount = 0;
        $optimoCount = 0;

        // Calcular promedio de cada estudiante para determinar riesgo/óptimo
        foreach ($curso->inscripciones as $inscripcion) {
            $estudianteId = $inscripcion->id_estudiante;
            
            $sumaNotas = 0;
            $pesoTotal = 0;
            foreach ($curso->tareas as $tarea) {
                $nota = $tarea->notas->where('id_estudiante', $estudianteId)->first();
                if ($nota) {
                    $sumaNotas += $nota->nota * ($tarea->porcentaje / 100);
                    $pesoTotal += $tarea->porcentaje;
                }
            }
            $promedio = $pesoTotal > 0 ? ($sumaNotas / ($pesoTotal / 100)) : 0;

            if ($promedio > 0 && $promedio < 3.0) {
                $riesgoCount++;
            } else if ($promedio > 4.5) {
                $optimoCount++;
            }
        }

        // Calcular rendimiento de las tareas
        $rendimientoTareas = [];
        $tareaDificil = null;

        foreach ($curso->tareas as $tarea) {
            $promedioTarea = $tarea->notas->avg('nota') ?? 0;
            $rendimiento = [
                'nombre' => $tarea->titulo,
                'promedio' => round($promedioTarea, 2)
            ];
            $rendimientoTareas[] = $rendimiento;

            if (!$tareaDificil || $promedioTarea < $tareaDificil['promedio']) {
                $tareaDificil = $rendimiento;
            }
        }

        // Si no hay tareas, proporcionar datos vacíos
        if (!$tareaDificil) {
            $tareaDificil = ['nombre' => 'N/A', 'promedio' => 0];
        }

        // Simular o calcular tendencia de notas y asistencias por semana/tarea
        $tendencia = [];
        $asistenciasPorFecha = $curso->asistencias->groupBy('fecha')->sortKeys();
        
        $semanaIndex = 1;
        foreach ($curso->tareas as $tarea) {
            $promedioNota = $tarea->notas->avg('nota') ?? 0;
            // Para propósitos visuales, calculamos una asistencia global hasta ese momento o simulamos
            // Aquí promediamos la asistencia global para no complejizar demasiado
            $totalAsistencias = $curso->asistencias->count();
            $presentes = $curso->asistencias->where('estado', 'presente')->count();
            $asistenciaPorcentaje = $totalAsistencias > 0 ? round(($presentes / $totalAsistencias) * 100) : 100;

            $tendencia[] = [
                'semana' => 'T' . $semanaIndex++,
                'nota' => round($promedioNota, 2),
                'asistencia' => $asistenciaPorcentaje
            ];
        }

        return response()->json([
            'riesgoCount' => $riesgoCount,
            'optimoCount' => $optimoCount,
            'tareaDificil' => $tareaDificil,
            'rendimientoTareas' => $rendimientoTareas,
            'tendencia' => $tendencia
        ]);
    }
}
