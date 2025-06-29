<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsoAmbiente extends Model
{
    use HasFactory;

    protected $table = 'uso_ambiente';
    protected $primaryKey = 'id_uso_ambiente';
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = [
        'USUARIO_id_usuario',
        'descripcion',
        'semestre',
        'fch_uso',
        'hora_uso',
        'AMBIENTE_id_ambiente',
        'PERSONAL_id_personal',
    ];

    //relaciones
    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'AMBIENTE_id_ambiente');
    }

    public function personalInicio()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }
    // Relación con Personal (Fin)
    public function personalFin()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'USUARIO_id_usuario');
    }

    public function finalUsos()
    {
        return $this->hasOne(FinalUso::class, 'USO_AMBIENTE_id_uso_ambiente', 'id_uso_ambiente');
    }
}
