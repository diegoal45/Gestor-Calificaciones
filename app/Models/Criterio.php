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

    public function rubrica()
    {
        return $this->belongsTo(Rubrica::class, 'id_rubrica');
    }
}
