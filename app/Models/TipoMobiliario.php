<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMobiliario extends Model
{
    use HasFactory;

    protected $table = 'tipo_mobiliario';
    
    protected $primaryKey = 'id_tipo_mueb';
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = [
        'tipo_mueble'
    ];

    // Relaciones
    public function mobiliarios()
    {
        return $this->hasMany(Mobiliario::class, 'TIPO_MOBILIARIO_id_tipo_mueb');
    }
}
