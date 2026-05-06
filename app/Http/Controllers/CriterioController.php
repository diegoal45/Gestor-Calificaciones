<?php

namespace App\Http\Controllers;

use App\Models\Criterio;
use App\Models\Rubrica;
use App\Http\Requests\CriterioRequest;
use Illuminate\Http\Request;

class CriterioController extends Controller
{
    // Listar criterios de una rúbrica con niveles
    public function index($rubricaId)
    {
        $criterios = Criterio::with('niveles')
            ->where('id_rubrica', $rubricaId)
            ->get();
        return response()->json($criterios);
    }

    // Mostrar un criterio específico con niveles
    public function show($id)
    {
        $criterio = Criterio::with('niveles')->findOrFail($id);
        return response()->json($criterio);
    }

    // Crear un nuevo criterio
    public function store(CriterioRequest $request, $rubricaId)
    {
        $data = $request->validated();
        $data['id_rubrica'] = $rubricaId;
        $criterio = Criterio::create($data);
        return response()->json($criterio, 201);
    }

    // Actualizar un criterio existente
    public function update(CriterioRequest $request, $id)
    {
        $criterio = Criterio::findOrFail($id);
        $criterio->update($request->validated());
        return response()->json($criterio);
    }

    // Eliminar un criterio
    public function destroy($id)
    {
        $criterio = Criterio::findOrFail($id);
        $criterio->delete();
        return response()->json(['message' => 'Criterio eliminado']);
    }

    // Exportar criterios y niveles de una rúbrica
    public function export($rubricaId)
    {
        $criterios = Criterio::with('niveles')
            ->where('id_rubrica', $rubricaId)
            ->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($criterios);
    }
}
