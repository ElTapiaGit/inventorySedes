<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipo';

    protected $primaryKey = 'cod_equipo';
    protected $keyType = 'string'; // Si la llave primaria es de tipo string
    public $incrementing = false; // Si la llave primaria no es autoincremental
    public $timestamps = false; // Desactivar los timestamps si no se usan
    
    protected $fillable = [
        'cod_equipo',
        'nombre_equi',
        'marca',
        'modelo',
        'descripcion_equi',
        'empotrado',
        'estado_equi',
        'observaciones_equi',
        'vida_util',
        'fch_registro',
        'AMBIENTE_id_ambiente',
        'FOTO_id_foto'
    ];

    // Relaciones
    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'AMBIENTE_id_ambiente');
    }

    // Relación con Foto
    public function foto()
    {
        return $this->belongsTo(Foto::class, 'FOTO_id_foto');
    }

    // Relación con Componentes
    public function componentes()
    {
        return $this->hasMany(Componente::class, 'EQUIPO_cod_equipo', 'cod_equipo');
    }

    // Relación muchos a muchos con accesorios a través de equipo_has_accesorio
    public function accesorios()
    {
        return $this->belongsToMany(Accesorio::class, 'equipo_has_accesorio', 'EQUIPO_cod_equipo', 'ACCESORIO_cod_accesorio');
    }
}
