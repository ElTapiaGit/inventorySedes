<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPersonal extends Model
{
    use HasFactory;

    protected $table = 'tipo_personal'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'id_tipo_per'; // Clave primaria de la tabla
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = [
        'descripcion_per'
    ];

    // Relación con el modelo Personal
    public function personal()
    {
        return $this->hasMany(Personal::class, 'TIPO_PERSONAL_id_tipo_per');
    }
}
