<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencia';
    public $timestamps = false;
    protected $fillable = [
        'id_curso',
        'id_estudiante',
        'fecha',
        'estado',
    ];

    /**
     * Curso al que pertenece la asistencia
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    /**
     * Estudiante asociado a la asistencia
     */
    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'id_estudiante');
    }
}
