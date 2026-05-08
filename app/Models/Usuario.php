<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol',
    ];

    /**
     * Cursos dictados por el usuario (profesor)
     */
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'id_profesor');
    }

    /**
     * Inscripciones del usuario (estudiante)
     */
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_estudiante');
    }

    /**
     * Notas del usuario (estudiante)
     */
    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_estudiante');
    }

    /**
     * Reclamos realizados por el usuario (estudiante)
     */
    public function reclamos()
    {
        return $this->hasMany(Reclamo::class, 'id_estudiante');
    }

    /**
     * Asistencias del usuario (estudiante)
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_estudiante');
    }
}
