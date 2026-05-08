<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionRubrica extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_rubrica';
    public $timestamps = false;
    protected $fillable = [
        'id_nota',
        'id_criterio',
        'id_nivel',
        'porcentaje',
    ];

    /**
     * Nota evaluada
     */
    public function nota()
    {
        return $this->belongsTo(Nota::class, 'id_nota');
    }

    /**
     * Criterio evaluado
     */
    public function criterio()
    {
        return $this->belongsTo(Criterio::class, 'id_criterio');
    }

    /**
     * Nivel del criterio
     */
    public function nivel()
    {
        return $this->belongsTo(NivelCriterio::class, 'id_nivel');
    }
}
