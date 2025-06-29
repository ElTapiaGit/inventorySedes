<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamo';
    protected $primaryKey = 'id_prestamo';
    public $timestamps = false;


    protected $fillable = [
        'nombre_solicitante',
        'descripcion_prestamo',
        'fch_prestamo',
        'hora_prestamo',
        'PERSONAL_id_personal',
    ];

    //relaciones
    public function detallePrestamos()
    {
        return $this->hasMany(DetallePrestamo::class, 'PRESTAMO_id_prestamo');
    }

    public function devolucion()
    {
        //return $this->hasMany(Devolucion::class, 'PRESTAMO_id_prestamo');
        return $this->hasOne(Devolucion::class, 'PRESTAMO_id_prestamo');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }
}
