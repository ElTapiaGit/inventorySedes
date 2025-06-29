<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'material';

    protected $primaryKey = 'cod_mate';
    protected $keyType = 'string'; // Si la llave primaria es de tipo string
    public $incrementing = false; // Si la llave primaria no es autoincremental
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = [
        'cod_mate',
        'tipo_mate',
        'descripcion_mate',
        'estado_mate',
        'observacion_mate',
        'fch_registrada',
        'AMBIENTE_id_ambiente',
        'FOTO_id_foto'
    ];

    // Relaciones
    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'AMBIENTE_id_ambiente');
    }

    public function foto()
    {
        return $this->belongsTo(Foto::class, 'FOTO_id_foto');
    }
}
