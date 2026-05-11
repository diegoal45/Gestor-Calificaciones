<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    public const METODO_PONDERACION = 'ponderacion';
    public const METODO_PROMEDIO = 'promedio';

    protected $table = 'cursos';
    protected $fillable = [
        'nombre',
        'descripcion',
        'id_profesor',
        'nota_minima_aprobatoria',
        'nota_maxima',
        'usa_asistencia',
        'peso_asistencia',
        'metodo_calificacion',
        'codigo_invitacion',
    ];

    /**
     * Profesor responsable del curso
     */
    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'id_profesor');
    }

    /**
     * Tareas del curso
     */
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_curso');
    }

    /**
     * Inscripciones al curso
     */
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_curso');
    }

    /**
     * Asistencias del curso
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_curso');
    }
}