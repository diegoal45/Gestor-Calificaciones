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
            'criterio_id' => 'required|exists:criterios,id',
            'nombre' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
        ];
    }
}
