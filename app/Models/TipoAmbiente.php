<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAmbiente extends Model
{
    use HasFactory;

    protected $table = 'tipo_ambiente';
    protected $primaryKey = 'id_tipoambiente';
    public $timestamps = false;

    protected $fillable = [
        'id_tipoambiente',
        'nombre_amb',
    ];

    //relacion
    /*public function ambientes()
    {
        return $this->hasMany(Ambiente::class, 'TIPO_AMBIENTE_id_ambiente', 'id_tipoambiente');
    }*/
}
