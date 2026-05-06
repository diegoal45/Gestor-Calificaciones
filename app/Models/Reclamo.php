<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    use HasFactory;

    protected $table = 'reclamos';
    protected $fillable = [
        'id_nota',
        'id_estudiante',
        'mensaje',
        'respuesta',
        'estado',
    ];

    /**
     * Nota asociada al reclamo
     */
    public function nota()
    {
        return $this->belongsTo(Nota::class, 'id_nota');
    }

    /**
     * Estudiante que realiza el reclamo
     */
    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'id_estudiante');
    }
}
