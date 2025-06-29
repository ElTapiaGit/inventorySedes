<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    use HasFactory;

    protected $table = 'componente';
    protected $primaryKey = 'id_compo';
    public $timestamps = false;

    protected $fillable = [
        'nombre_compo', 
        'descripcion_compo', 
        'estado_compo', 
        'EQUIPO_cod_equipo'
    ];

    //relacion
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'EQUIPO_cod_equipo', 'cod_equipo');
    }
}
