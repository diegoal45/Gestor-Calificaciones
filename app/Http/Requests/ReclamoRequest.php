<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReclamoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_estudiante' => 'required|exists:usuarios,id',
            'mensaje' => 'required|string',
        ];
    }
}
