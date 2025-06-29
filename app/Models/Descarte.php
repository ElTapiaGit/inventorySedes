<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descarte extends Model
{
    use HasFactory;

    protected $table = 'descarte'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'id_descarte'; // Nombre de la clave primaria en la tabla
    public $timestamps = false;
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descrpcion_descarte',
        'orden_desacarte',
        'fch_descarte',
        'PERSONAL_id_personal'
    ];

    //relacion
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }
}
