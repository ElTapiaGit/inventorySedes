<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesMantenimiento extends Model
{
    use HasFactory;

    // Definimos la tabla asociada al modelo
    protected $table = 'detalles_mantenimiento';
    protected $primaryKey = 'id_mante';
    public $timestamps = false;

    // Definimos los campos que pueden ser asignados masivamente
    protected $fillable = [
        'cod_articulo',
        'INICIO_MANTENIMIENTO_id_mantenimiento_ini'
    ];

    // Relación con el modelo `Mantenimiento`
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'INICIO_MANTENIMIENTO_id_mantenimiento_ini');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'cod_articulo', 'Cod_equipo');
    }
}
