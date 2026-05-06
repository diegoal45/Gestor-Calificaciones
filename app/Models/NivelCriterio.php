<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelCriterio extends Model
{
    use HasFactory;

    protected $table = 'niveles_criterio';
    public $timestamps = false;
    protected $fillable = [
        'id_criterio',
        'nombre',
        'valor',
    ];

    public function criterio()
    {
        return $this->belongsTo(Criterio::class, 'id_criterio');
    }
}
