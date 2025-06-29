<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAccesorio extends Model
{
    use HasFactory;

    protected $table = 'historial_accesorio';
    protected $primaryKey = 'id_historial';
    //relacion directa o compuesta
    //protected $primaryKey = ['id_historial', 'ACCESORIO_cod_accesorio'];
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'motivo_cambio',
        'fch_cambio',
        'ACCESORIO_id_accesorio'
    ];

    // Relación con el accesorio
    public function accesorio()
    {
        return $this->belongsTo(Accesorio::class, 'ACCESORIO_cod_accesorio');
    }
}
