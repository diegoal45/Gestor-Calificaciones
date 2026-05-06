<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';
    public $timestamps = false;
    protected $fillable = [
        'id_curso',
        'id_estudiante',
        'grupo',
    ];

    /**
     * Curso al que pertenece la inscripción
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    /**
     * Estudiante inscrito
     */
    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'id_estudiante');
    }
}