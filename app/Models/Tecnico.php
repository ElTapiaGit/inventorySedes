<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    use HasFactory;

    // Definimos la tabla asociada al modelo
    protected $table = 'tecnico';
    protected $primaryKey = 'id_tecnico';
    public $timestamps = false;

    // Definimos los campos que pueden ser asignados masivamente
    protected $fillable = [
        'nombre',
        'ap_paterno',
        'ap_materno',
        'celular',
        'direccion'
    ];

    // Relación con el modelo `Mantenimiento`
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'TECNICO_id_tecnico');
    }
}
