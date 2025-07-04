<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalMantenimiento extends Model
{
    use HasFactory;

    // Definimos la tabla asociada al modelo
    protected $table = 'final_mantenimiento';
    protected $primaryKey = 'id_mante_final';
    public $timestamps = false;

    // Definimos los campos que pueden ser asignados masivamente
    protected $fillable = [
        'informe_final',
        'fch_final',
        'INICIO_MANTENIMIENTO_id_mantenimiento_ini'
    ];

    // Relación con el modelo `Mantenimiento`
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'INICIO_MANTENIMIENTO_id_mantenimiento_ini');
    }
}
