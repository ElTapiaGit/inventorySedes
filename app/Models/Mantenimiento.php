<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    // Definimos la tabla asociada al modelo
    protected $table = 'inicio_mantenimiento';
    protected $primaryKey = 'id_mantenimiento_ini';
    public $timestamps = false;

    // Definimos los campos que pueden ser asignados masivamente
    protected $fillable = [
        'informe_inicial',
        'fch_inicio',
        'TECNICO_id_tecnico',
        'PERSONAL_id_personal'
    ];

    // Definimos la relación con la tabla `detalles_mantenimiento`
    public function detalleMantenimiento()
    {
        return $this->hasMany(DetallesMantenimiento::class, 'INICIO_MANTENIMIENTO_id_mantenimiento_ini');
    }

    // Definimos la relación con la tabla `tecnico`
    public function tecnico()
    {
        return $this->belongsTo(Tecnico::class, 'TECNICO_id_tecnico');
    }

    // Definimos la relación con la tabla `personal`
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }

    // Definimos la relación con la tabla `final_mantenimiento`
    public function finalMantenimiento()
    {
        return $this->hasOne(FinalMantenimiento::class, 'INICIO_MANTENIMIENTO_id_mantenimiento_ini');
    }
}
