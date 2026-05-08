<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TareaRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $tipo = $this->input('tipo');
        if (is_string($tipo)) {
            $this->merge(['tipo' => strtolower(trim($tipo))]);
        }

        if (!$this->input('id_curso')) {
            $this->merge([
                'id_curso' => $this->route('cursoId'),
            ]);
        }
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $cursoIdRule = $this->isMethod('post') ? 'required|exists:cursos,id' : 'nullable|exists:cursos,id';

        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            // Compatibilidad con frontend y modelo actual
            'id_curso' => $cursoIdRule,
            'tipo' => 'required|in:manual,rubrica',
            'fecha_limite' => 'required|date',
            'porcentaje' => 'required|numeric|min:0|max:100',
        ];
    }
}
