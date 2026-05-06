<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'valor' => 'required|numeric|min:0',
            'tarea_id' => 'required|exists:tareas,id',
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'feedback' => 'nullable|string',
        ];
    }
}
