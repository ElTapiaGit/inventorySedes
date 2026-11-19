<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edificio extends Model
{
    use HasFactory;
    //Especificamos el nombre de la tabla sin la 's'
    protected $table = 'edificio';

    protected $primaryKey = 'id_edificio';
    public $timestamps = false;

    //resaltamos los campos que se trabaran para editar
    protected $fillable = [
        'nombre_edi',
        'direccion',
        'SEDE_id_sede',
        'CARRERA_id_carrera',
    ];

    //define la relacion hasMany donde un Edificio puede tener muchos Piso
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'SEDE_id_sede');
    }
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'CARRERA_id_carrera', 'id_carrera');
    }
    public function pisos()
    {
        return $this->hasMany(Piso::class, 'EDIFICIO_id_edificio', 'id_edificio');
    }
    // Relación con el modelo Personal
    public function personal()
    {
        return $this->hasMany(Personal::class, 'EDIFICIO_id_edificio');
    }
}
