<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TareaRequest extends FormRequest
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
            'curso_id' => 'required|exists:cursos,id',
            'tipo_evaluacion' => 'required|in:manual,rubrica',
            'fecha_entrega' => 'required|date',
            'peso' => 'required|numeric|min:0|max:100',
        ];
    }
}
