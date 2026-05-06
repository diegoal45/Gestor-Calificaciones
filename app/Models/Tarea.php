<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';
    public $timestamps = false;
    protected $fillable = [
        'id_curso',
        'nombre',
        'descripcion',
        'porcentaje',
        'tipo',
        'fecha_limite',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }
}
