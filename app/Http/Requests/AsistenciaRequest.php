<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsistenciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'fecha' => 'required|date',
            'presente' => 'required|boolean',
        ];
    }
}
