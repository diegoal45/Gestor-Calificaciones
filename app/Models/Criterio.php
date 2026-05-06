<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterio extends Model
{
    use HasFactory;

    protected $table = 'criterios';
    public $timestamps = false;
    protected $fillable = [
        'id_rubrica',
        'nombre',
        'peso',
    ];

    /**
     * Rúbrica asociada al criterio
     */
    public function rubrica()
    {
        return $this->belongsTo(Rubrica::class, 'id_rubrica');
    }

    /**
     * Niveles del criterio
     */
    public function niveles()
    {
        return $this->hasMany(NivelCriterio::class, 'id_criterio');
    }

    /**
     * Evaluaciones asociadas al criterio
     */
    public function evaluacionesRubrica()
    {
        return $this->hasMany(EvaluacionRubrica::class, 'id_criterio');
    }
}
