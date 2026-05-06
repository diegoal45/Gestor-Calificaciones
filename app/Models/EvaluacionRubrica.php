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
    ];

    public function nota()
    {
        return $this->belongsTo(Nota::class, 'id_nota');
    }

    public function criterio()
    {
        return $this->belongsTo(Criterio::class, 'id_criterio');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelCriterio::class, 'id_nivel');
    }
}
