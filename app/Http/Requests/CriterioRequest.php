<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriterioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'rubrica_id' => 'required|exists:rubricas,id',
            'peso' => 'required|numeric|min:0|max:100',
        ];
    }
}
