<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $fillable = [
        'nombre',
        'descripcion',
        'id_profesor',
        'nota_minima_aprobatoria',
        'nota_maxima',
        'usa_asistencia',
        'peso_asistencia',
    ];

    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'id_profesor');
    }
}