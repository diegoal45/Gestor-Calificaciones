<?php

namespace Database\Factories;

use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'id_profesor' => 1, // Debe ser reemplazado al crear el curso
            'nota_minima_aprobatoria' => 11,
            'nota_maxima' => 20,
            'usa_asistencia' => $this->faker->boolean(),
            'peso_asistencia' => $this->faker->randomFloat(2, 0, 20),
            'metodo_calificacion' => $this->faker->randomElement([Curso::METODO_PONDERACION, Curso::METODO_PROMEDIO]),
            'codigo_invitacion' => strtoupper($this->faker->unique()->bothify('INVITE####')),
        ];
    }
}
