<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialNota extends Model
{
    use HasFactory;

    protected $table = 'historial_notas';
    public $timestamps = false;
    protected $fillable = [
        'id_nota',
        'nota_anterior',
        'nota_nueva',
        'changed_at',
    ];

    /**
     * Nota asociada al historial
     */
    public function nota()
    {
        return $this->belongsTo(Nota::class, 'id_nota');
    }
}
