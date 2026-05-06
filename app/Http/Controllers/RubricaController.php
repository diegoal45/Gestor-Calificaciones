<?php

namespace App\Http\Controllers;

use App\Models\Rubrica;
use App\Models\Tarea;
use App\Http\Requests\RubricaRequest;
use Illuminate\Http\Request;

class RubricaController extends Controller
{
    // Listar rúbricas de una tarea con criterios y niveles
    public function index($tareaId)
    {
        $rubricas = Rubrica::with('criterios.niveles')
            ->where('id_tarea', $tareaId)
            ->get();
        return response()->json($rubricas);
    }

    // Mostrar una rúbrica específica con criterios y niveles
    public function show($id)
    {
        $rubrica = Rubrica::with('criterios.niveles')->findOrFail($id);
        return response()->json($rubrica);
    }

    // Crear una nueva rúbrica
    public function store(RubricaRequest $request, $tareaId)
    {
        $data = $request->validated();
        $data['id_tarea'] = $tareaId;
        $rubrica = Rubrica::create($data);
        return response()->json($rubrica, 201);
    }

    // Actualizar una rúbrica existente
    public function update(RubricaRequest $request, $id)
    {
        $rubrica = Rubrica::findOrFail($id);
        $rubrica->update($request->validated());
        return response()->json($rubrica);
    }

    // Eliminar una rúbrica
    public function destroy($id)
    {
        $rubrica = Rubrica::findOrFail($id);
        $rubrica->delete();
        return response()->json(['message' => 'Rúbrica eliminada']);
    }

    // Exportar rúbricas y criterios de una tarea
    public function export($tareaId)
    {
        $rubricas = Rubrica::with('criterios.niveles')
            ->where('id_tarea', $tareaId)
            ->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($rubricas);
    }
}
