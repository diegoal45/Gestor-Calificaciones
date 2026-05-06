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
            'nota_id' => 'required|exists:notas,id',
            'motivo' => 'required|string',
            'estado' => 'nullable|in:pendiente,respondido,cerrado',
            'respuesta' => 'nullable|string',
        ];
    }
}
