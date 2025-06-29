<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoHasAccesorio extends Model
{
    use HasFactory;
    // Definir la tabla asociada al modelo
    protected $table = 'equipo_has_accesorio';
    public $timestamps = false;

    protected $fillable = [
        'EQUIPO_cod_equipo',
        'ACCESORIO_cod_accesorio',
    ];

    // Definir la relación con el modelo Equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'EQUIPO_cod_equipo', 'cod_equipo');
    }

    // Definir la relación con el modelo Accesorio
    public function accesorio()
    {
        return $this->belongsTo(Accesorio::class, 'ACCESORIO_cod_accesorio', 'cod_accesorio');
    }
}
