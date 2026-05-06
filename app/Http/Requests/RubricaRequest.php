<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RubricaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'tarea_id' => 'required|exists:tareas,id',
        ];
    }
}
