<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NivelCriterioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'valor' => 'nullable|numeric|min:0|max:100',
            'descripcion' => 'nullable|string',
        ];
    }
}
