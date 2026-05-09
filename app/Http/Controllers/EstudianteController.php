<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Nota;

class EstudianteController extends Controller
{
    private function calcularPromedioPonderado($curso, int $estudianteId): float
    {
        $sumaNotas = 0;
        $pesoTotal = 0;

        foreach ($curso->tareas as $tarea) {
            $notaRecord = $tarea->notas->where('id_estudiante', $estudianteId)->first();
            if ($notaRecord) {
                $sumaNotas += $notaRecord->nota * ($tarea->porcentaje / 100);
                $pesoTotal += $tarea->porcentaje;
            }
        }

        if ($pesoTotal <= 0) return 0;
        return $sumaNotas / ($pesoTotal / 100);
    }

    /**
     * Obtiene el perfil de un estudiante para un curso específico
     */
    public function perfil(Request $request, int $id)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id'
        ]);

        $cursoId = $request->curso_id;
        $estudiante = Usuario::findOrFail($id);

        // Asegurarse de que el usuario es un estudiante
        if ($estudiante->rol !== 'estudiante') {
            return response()->json(['message' => 'El usuario no es un estudiante'], 400);
        }

        $curso = Curso::with(['tareas.notas.evaluacionesRubrica.criterio', 'tareas.notas.evaluacionesRubrica.nivel'])->findOrFail($cursoId);

        $tareasList = [];
        $sumaNotas = 0;
        $pesoTotal = 0;

        foreach ($curso->tareas as $tarea) {
            $notaRecord = $tarea->notas->where('id_estudiante', $id)->first();
            
            if ($notaRecord) {
                $sumaNotas += $notaRecord->nota * ($tarea->porcentaje / 100);
                $pesoTotal += $tarea->porcentaje;
            }

            $tareasList[] = [
                'id' => $tarea->id,
                'nombre' => $tarea->nombre,
                'tipo' => $tarea->tipo,
                'porcentaje' => $tarea->porcentaje,
                'nota_id' => $notaRecord ? $notaRecord->id : null,
                'nota' => $notaRecord ? number_format($notaRecord->nota, 1) : null,
                'feedback' => $notaRecord ? $notaRecord->feedback : null,
                'rubrica_evaluacion' => $notaRecord
                    ? collect($notaRecord->evaluacionesRubrica ?? [])->map(function ($ev) {
                        return [
                            'criterio' => [
                                'id' => $ev->criterio?->id,
                                'nombre' => $ev->criterio?->nombre,
                                'peso' => $ev->criterio?->peso,
                            ],
                            'nivel' => [
                                'id' => $ev->nivel?->id,
                                'nombre' => $ev->nivel?->nombre,
                                'valor' => $ev->nivel?->valor,
                                'descripcion' => $ev->nivel?->descripcion,
                            ],
                            'porcentaje' => $ev->porcentaje,
                        ];
                    })->values()
                    : [],
            ];
        }

        $promedio = $pesoTotal > 0 ? ($sumaNotas / ($pesoTotal / 100)) : 0;
        
        $estado = 'Normal';
        $riesgo = 'Bajo';
        
        if ($promedio >= 4.0) { $estado = 'Excelente'; }
        else if ($promedio >= $curso->nota_minima_aprobatoria) { $estado = 'Bueno'; }
        else if ($promedio > 0 && $promedio < 3.0) { $estado = 'Crítico'; $riesgo = 'Alto'; }
        else if ($promedio == 0) { $estado = 'Sin notas'; }

        return response()->json([
            'estudiante' => [
                'id' => $estudiante->id,
                'nombre' => $estudiante->nombre,
                'email' => $estudiante->email,
                'promedio' => $promedio > 0 ? number_format($promedio, 1) : '-',
                'estado' => $estado,
                'riesgo' => $riesgo
            ],
            'tareas' => $tareasList
        ]);
    }

    /**
     * Analisis personal comparado con el curso (percentil y posicion)
     */
    public function analisis(Request $request, int $id)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id'
        ]);

        $cursoId = (int) $request->curso_id;
        $curso = Curso::with(['tareas.notas', 'inscripciones'])->findOrFail($cursoId);
        $estudiante = Usuario::findOrFail($id);

        if ($estudiante->rol !== 'estudiante') {
            return response()->json(['message' => 'El usuario no es un estudiante'], 400);
        }

        $promedios = [];
        foreach ($curso->inscripciones as $inscripcion) {
            $prom = $this->calcularPromedioPonderado($curso, (int) $inscripcion->id_estudiante);
            if ($prom > 0) {
                $promedios[(int) $inscripcion->id_estudiante] = $prom;
            }
        }

        $promedioEstudiante = $promedios[$id] ?? 0;
        if ($promedioEstudiante <= 0 || count($promedios) === 0) {
            return response()->json([
                'promedio_estudiante' => 0,
                'promedio_curso' => 0,
                'mediana_curso' => 0,
                'percentil' => 0,
                'posicion' => null,
                'total_estudiantes' => count($curso->inscripciones),
                'mensaje' => 'Aun no hay suficientes notas para calcular analisis comparativo.',
            ]);
        }

        arsort($promedios); // mayor a menor
        $rankingIds = array_keys($promedios);
        $posicion = array_search($id, $rankingIds, true);
        $posicion = $posicion === false ? null : $posicion + 1;

        $promedioCurso = array_sum($promedios) / count($promedios);
        $sortedValues = array_values($promedios);
        sort($sortedValues);
        $n = count($sortedValues);
        $medianaCurso = $n % 2 === 0
            ? ($sortedValues[$n / 2 - 1] + $sortedValues[$n / 2]) / 2
            : $sortedValues[(int) floor($n / 2)];

        $inferiores = 0;
        foreach ($promedios as $prom) {
            if ($prom < $promedioEstudiante) $inferiores++;
        }
        $percentil = round(($inferiores / max(1, count($promedios))) * 100);

        return response()->json([
            'promedio_estudiante' => round($promedioEstudiante, 2),
            'promedio_curso' => round($promedioCurso, 2),
            'mediana_curso' => round($medianaCurso, 2),
            'percentil' => $percentil,
            'posicion' => $posicion,
            'total_estudiantes' => count($promedios),
            'mensaje' => $percentil >= 75
                ? 'Excelente: estas en el tramo superior del curso.'
                : ($percentil >= 50
                    ? 'Vas bien: estas sobre la mitad del curso.'
                    : 'Atencion: estas bajo la mitad del curso, conviene reforzar estudio.'),
        ]);
    }
}
