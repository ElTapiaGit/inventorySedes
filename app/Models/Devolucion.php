<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devolucion';
    protected $primaryKey = 'id_devolucion';
    public $timestamps = false;

    protected $fillable = [
        'fch_devolucion',
        'hora_devolucion',
        'descripcion_devolucion',
        'PRESTAMO_id_prestamo',
        'PERSONAL_id_personal',
    ];

    //relaciones
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'PRESTAMO_id_prestamo');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }
}
