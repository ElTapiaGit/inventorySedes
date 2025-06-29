<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePrestamo extends Model
{
    use HasFactory;

    protected $table = 'detalle_prestamo';
    protected $primaryKey = 'id_detalle_pres';
    public $timestamps = false;

    protected $fillable = [
        'cod_articulo',
        'observacion_detalle',
        'PRESTAMO_id_prestamo',
        'AMBIENTE_id_ambiente',
    ];

    //relaciones
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'PRESTAMO_id_prestamo');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'AMBIENTE_id_ambiente');
    }

    //metodos de relacion para ayudar a obtener los articulo equipo, material, mobiliario... segun cod_articulo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'cod_articulo', 'Cod_equipo');
    }
    public function mobiliario()
    {
        return $this->belongsTo(Mobiliario::class, 'cod_articulo', 'cod_mueble');
    }
    public function material()
    {
        return $this->belongsTo(Material::class, 'cod_articulo', 'cod_mate');
    }
    public function accesorio()
    {
        return $this->belongsTo(Accesorio::class, 'cod_articulo', 'cod_accesorio');
    }

}
