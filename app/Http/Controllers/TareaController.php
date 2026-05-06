<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Curso;
use App\Http\Requests\TareaRequest;
use Illuminate\Http\Request;

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
        $tarea = Tarea::create($data);
        return response()->json($tarea, 201);
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
