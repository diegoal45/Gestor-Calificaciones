<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas';
    protected $fillable = [
        'id_tarea',
        'id_estudiante',
        'nota',
        'feedback',
    ];

    /**
     * Tarea asociada a la nota
     */
    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'id_tarea');
    }

    /**
     * Estudiante dueño de la nota
     */
    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'id_estudiante');
    }

    /**
     * Historial de cambios de la nota
     */
    public function historialNotas()
    {
        return $this->hasMany(HistorialNota::class, 'id_nota');
    }

    /**
     * Evaluaciones por rúbrica
     */
    public function evaluacionesRubrica()
    {
        return $this->hasMany(EvaluacionRubrica::class, 'id_nota');
    }
}
