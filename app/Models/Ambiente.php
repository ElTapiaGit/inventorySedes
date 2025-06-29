<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambiente extends Model
{
    use HasFactory;

    protected $table = 'ambiente';
    protected $primaryKey = 'id_ambiente';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion_amb',
        'TIPO_AMBIENTE_id_ambiente',
        'PISO_id_piso',
    ];

    public function tipoAmbiente()
    {
        return $this->belongsTo(TipoAmbiente::class, 'TIPO_AMBIENTE_id_ambiente', 'id_tipoambiente');
    }

    public function piso()
    {
        return $this->belongsTo(Piso::class, 'PISO_id_piso', 'id_piso');
    }


    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'AMBIENTE_id_ambiente');
    }

    public function mobiliarios()
    {
        return $this->hasMany(Mobiliario::class, 'AMBIENTE_id_ambiente');
    }

    public function materiales()
    {
        return $this->hasMany(Material::class, 'AMBIENTE_id_ambiente');
    }
}
