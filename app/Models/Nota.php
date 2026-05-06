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

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'id_tarea');
    }

    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'id_estudiante');
    }
}
