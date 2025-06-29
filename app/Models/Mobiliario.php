<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobiliario extends Model
{
    use HasFactory;

    protected $table = 'mobiliario';

    protected $primaryKey = 'cod_mueble';
    protected $keyType = 'string'; // Si la llave primaria es de tipo string
    public $incrementing = false; // Si la llave primaria no es autoincremental
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = [
        'cod_mueble',
        'descripticion_mueb',
        'fch_registro',
        'estado_mueb',
        'observacion',
        'vida_util',
        'TIPO_MOBILIARIO_id_tipo_mueb',
        'AMBIENTE_id_ambiente',
        'FOTO_id_foto'
    ];
    
    // Relaciones
    public function tipoMobiliario()
    {
        return $this->belongsTo(TipoMobiliario::class, 'TIPO_MOBILIARIO_id_tipo_mueb');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'AMBIENTE_id_ambiente');
    }

    public function foto()
    {
        return $this->belongsTo(Foto::class, 'FOTO_id_foto');
    }
}
