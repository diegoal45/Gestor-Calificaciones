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
            'nota' => 'required|numeric|min:0|max:5',
            'id_estudiante' => 'required|exists:usuarios,id',
            'feedback' => 'nullable|string',
        ];
    }
}
