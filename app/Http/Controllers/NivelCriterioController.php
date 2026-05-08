<?php

namespace App\Http\Controllers;

use App\Models\NivelCriterio;
use App\Models\Criterio;
use App\Http\Requests\NivelCriterioRequest;
use Illuminate\Http\Request;

class NivelCriterioController extends Controller
{
    // Listar niveles de un criterio
    public function index($criterioId)
    {
        $niveles = NivelCriterio::where('id_criterio', $criterioId)->get();
        return response()->json($niveles);
    }

    // Mostrar un nivel específico
    public function show($id)
    {
        $nivel = NivelCriterio::findOrFail($id);
        return response()->json($nivel);
    }

    // Crear un nuevo nivel
    public function store(NivelCriterioRequest $request, $criterioId)
    {
        $data = $request->validated();
        $data['id_criterio'] = $criterioId;
        if (!array_key_exists('valor', $data) || $data['valor'] === null) {
            $data['valor'] = 0;
        }
        $nivel = NivelCriterio::create($data);
        return response()->json($nivel, 201);
    }

    // Actualizar un nivel existente
    public function update(NivelCriterioRequest $request, $id)
    {
        $nivel = NivelCriterio::findOrFail($id);
        $data = $request->validated();
        if (!array_key_exists('valor', $data) || $data['valor'] === null) {
            $data['valor'] = 0;
        }
        $nivel->update($data);
        return response()->json($nivel);
    }

    // Eliminar un nivel
    public function destroy($id)
    {
        $nivel = NivelCriterio::findOrFail($id);
        $nivel->delete();
        return response()->json(['message' => 'Nivel eliminado']);
    }

    // Exportar niveles de un criterio
    public function export($criterioId)
    {
        $niveles = NivelCriterio::where('id_criterio', $criterioId)->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($niveles);
    }
}
