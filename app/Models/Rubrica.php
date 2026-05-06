<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubrica extends Model
{
    use HasFactory;

    protected $table = 'rubricas';
    public $timestamps = false;
    protected $fillable = [
        'id_tarea',
        'nombre',
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'id_tarea');
    }
}
