<?php

namespace App\Http\Controllers;

use App\Models\Reclamo;
use App\Models\Nota;
use App\Models\Usuario;
use App\Http\Requests\ReclamoRequest;
use Illuminate\Http\Request;

class ReclamoController extends Controller
{
    // Listar reclamos de un estudiante
    public function indexEstudiante($estudianteId)
    {
        $reclamos = Reclamo::with('nota')
            ->where('id_estudiante', $estudianteId)
            ->get();
        return response()->json($reclamos);
    }

    // Listar reclamos de una nota
    public function indexNota($notaId)
    {
        $reclamos = Reclamo::with('estudiante')
            ->where('id_nota', $notaId)
            ->get();
        return response()->json($reclamos);
    }

    // Crear un nuevo reclamo
    public function store(ReclamoRequest $request, $notaId)
    {
        $data = $request->validated();
        $data['id_nota'] = $notaId;
        $data['estado'] = 'pendiente';
        $reclamo = Reclamo::create($data);
        return response()->json($reclamo, 201);
    }

    // Responder un reclamo
    public function responder(Request $request, $id)
    {
        $reclamo = Reclamo::findOrFail($id);
        $request->validate(['respuesta' => 'required|string']);
        $reclamo->respuesta = $request->respuesta;
        $reclamo->estado = 'respondido';
        $reclamo->save();
        return response()->json(['message' => 'Reclamo respondido', 'reclamo' => $reclamo]);
    }

    // Cerrar un reclamo
    public function cerrar($id)
    {
        $reclamo = Reclamo::findOrFail($id);
        $reclamo->estado = 'cerrado';
        $reclamo->save();
        return response()->json(['message' => 'Reclamo cerrado', 'reclamo' => $reclamo]);
    }

    // Eliminar un reclamo
    public function destroy($id)
    {
        $reclamo = Reclamo::findOrFail($id);
        $reclamo->delete();
        return response()->json(['message' => 'Reclamo eliminado']);
    }

    // Exportar reclamos de un estudiante
    public function export($estudianteId)
    {
        $reclamos = Reclamo::with('nota')
            ->where('id_estudiante', $estudianteId)
            ->get();
        // Aquí puedes implementar exportación a CSV/Excel
        // Por ahora, solo retorna los datos en JSON
        return response()->json($reclamos);
    }
}
