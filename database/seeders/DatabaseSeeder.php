<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Curso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear muchos usuarios (profesores y estudiantes)
        $profesor = Usuario::factory()->create([
            'nombre' => 'Profesor Principal',
            'email' => 'dtc5915911@gmail.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
        ]);

        // Crear más profesores y estudiantes
        Usuario::factory(5)->create(['rol' => 'profesor']);
        Usuario::factory(20)->create(['rol' => 'estudiante']);

        // Crear muchos cursos y asignar uno al profesor principal
        $cursoPrincipal = Curso::factory()->create([
            'nombre' => 'Curso de Prueba',
            'descripcion' => 'Curso asignado al profesor principal',
            'id_profesor' => $profesor->id,
            'nota_minima_aprobatoria' => 11,
            'nota_maxima' => 20,
            'usa_asistencia' => true,
            'peso_asistencia' => 10,
            'codigo_invitacion' => 'INVITAPRINCIPAL',
        ]);

        // Crear más cursos con profesores aleatorios
        $profesores = Usuario::where('rol', 'profesor')->pluck('id');
        Curso::factory(10)->create([
            'id_profesor' => $profesores->random(),
        ]);
    }
}
