<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Curso;
use App\Models\Rubrica;
use App\Models\Criterio;
use App\Models\NivelCriterio;
use App\Http\Requests\TareaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TareaController extends Controller
{
    // Listar tareas de un curso con notas
    public function index($cursoId)
    {
        $tareas = Tarea::with('notas')
            ->where('id_curso', $cursoId)
            ->get();
        return response()->json($tareas);
    }

    // Mostrar una tarea específica con notas y rúbricas
    public function show($id)
    {
        $tarea = Tarea::with(['notas', 'rubricas.criterios.niveles'])
            ->findOrFail($id);
        return response()->json($tarea);
    }

    // Crear una nueva tarea
    public function store(TareaRequest $request, $cursoId)
    {
        $data = $request->validated();
        $data['id_curso'] = $cursoId;

        $tarea = DB::transaction(function () use ($data) {
            $tarea = Tarea::create($data);

            if (($tarea->tipo ?? 'manual') === 'rubrica') {
                $this->crearRubricasPorDefecto($tarea->id);
            }

            return $tarea;
        });

        return response()->json($tarea, 201);
    }

    private function crearRubricasPorDefecto(int $tareaId): void
    {
        $plantillas = [
            [
                'nombre' => 'Rúbrica de Ensayo',
                'criterios' => [
                    ['nombre' => 'Claridad', 'peso' => 40],
                    ['nombre' => 'Argumentación', 'peso' => 60],
                ],
            ],
            [
                'nombre' => 'Presentación Oral',
                'criterios' => [
                    ['nombre' => 'Dominio del tema', 'peso' => 50],
                    ['nombre' => 'Comunicación', 'peso' => 50],
                ],
            ],
        ];

        // Moodle-like: 3 niveles por criterio con % (0-100). El profesor edita descripciones luego.
        $niveles = [
            ['nombre' => 'Mala', 'valor' => 0, 'descripcion' => null],
            ['nombre' => 'Buena', 'valor' => 60, 'descripcion' => null],
            ['nombre' => 'Excelente', 'valor' => 100, 'descripcion' => null],
        ];

        foreach ($plantillas as $plantilla) {
            $rubrica = Rubrica::create([
                'id_tarea' => $tareaId,
                'nombre' => $plantilla['nombre'],
            ]);

            foreach ($plantilla['criterios'] as $criterioData) {
                $criterio = Criterio::create([
                    'id_rubrica' => $rubrica->id,
                    'nombre' => $criterioData['nombre'],
                    'peso' => $criterioData['peso'],
                ]);

                foreach ($niveles as $nivelData) {
                    NivelCriterio::create([
                        'id_criterio' => $criterio->id,
                        'nombre' => $nivelData['nombre'],
                        'valor' => $nivelData['valor'],
                        'descripcion' => $nivelData['descripcion'],
                    ]);
                }
            }
        }
    }

    // Actualizar una tarea existente
    public function update(TareaRequest $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->update($request->validated());
        return response()->json($tarea);
    }

    // Eliminar una tarea
    public function destroy($id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->delete();
        return response()->json(['message' => 'Tarea eliminada']);
    }

    // Exportar tareas y notas de un curso
    public function export($cursoId)
    {
        $tareas = Tarea::with('notas')
            ->where('id_curso', $cursoId)
            ->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($tareas);
    }
}
