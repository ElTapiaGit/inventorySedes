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

    //definir un accesor para identificar automáticamente el tipo de artículo
    public function getNombreArticuloAttribute()
    {
        $codigo = $this->cod_articulo;

        $equipo = \App\Models\Equipo::where('Cod_equipo', $codigo)->with('ambiente')->first();
        if ($equipo) {
            return $equipo->nombre_equi . ' (Equipo)';
        }

        $material = \App\Models\Material::where('cod_mate', $codigo)->with('ambiente')->first();
        if ($material) {
            return $material->descripcion_mate . ' (Material)';
        }

        $mueble = \App\Models\Mobiliario::where('cod_mueble', $codigo)->with('ambiente')->first();
        if ($mueble) {
            return $mueble->descripticion_mueb . ' (Mobiliario)';
        }

        return 'Artículo no identificado';
    }

    public function getNombreAmbienteAttribute()
    {
        $codigo = $this->cod_articulo;

        $equipo = \App\Models\Equipo::where('Cod_equipo', $codigo)->with('ambiente')->first();
        if ($equipo && $equipo->ambiente) {
            return $equipo->ambiente->nombre;
        }

        $material = \App\Models\Material::where('cod_mate', $codigo)->with('ambiente')->first();
        if ($material && $material->ambiente) {
            return $material->ambiente->nombre;
        }

        $mueble = \App\Models\Mobiliario::where('cod_mueble', $codigo)->with('ambiente')->first();
        if ($mueble && $mueble->ambiente) {
            return $mueble->ambiente->nombre;
        }

        return '—';
    }


}
