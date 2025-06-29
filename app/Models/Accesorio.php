<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accesorio extends Model
{
    use HasFactory;

    protected $table = 'accesorio'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'cod_accesorio'; // Nombre de la clave primaria en la tabla
    public $incrementing = false;
    protected $keyType = 'string';//para mostrar la llave primaria

    // Aquí puedes definir los campos que son modificables
    protected $fillable = [
        'cod_accesorio',
        'nombre_acce',
        'descripcion_acce',
        'observacion_ace',
        'estado_acce',
        'vida_util',
        'ubicacion',
        'fch_registro_acce',
        'FOTO_id_foto'
    ];

    // Si los timestamps (created_at y updated_at) no son necesarios, puedes desactivarlos
    public $timestamps = false;
    //llave foranea
    public function foto()
    {
        return $this->belongsTo(Foto::class, 'FOTO_id_foto', 'id_foto');
    }

    // Relación muchos a muchos con equipos a través de equipo_has_accesorio
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_has_accesorio', 'ACCESORIO_cod_accesorio', 'EQUIPO_cod_equipo');
    }

    // Relación uno a muchos con historial_accesorio
    public function historial()
    {
        return $this->hasMany(HistorialAccesorio::class, 'ACCESORIO_cod_accesorio');
    }

    
}
