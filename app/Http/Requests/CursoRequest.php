<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CursoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'nota_minima' => 'required|numeric|min:0',
            'nota_maxima' => 'required|numeric|gt:nota_minima',
            'metodo_calificacion' => 'sometimes|string|in:ponderacion,promedio',
            'usa_asistencia' => 'sometimes|boolean',
            'peso_asistencia' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
