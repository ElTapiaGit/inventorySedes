<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piso extends Model
{
    use HasFactory;
    //Especificamos el nombre de la tabla sin la 's'
    protected $table = 'piso';
    protected $primaryKey = 'id_piso';
    public $timestamps = false;

    protected $fillable = [//llamar a los campos para hacer accciones como edit o agregar varios en uno solo
        'numero_piso',
        'EDIFICIO_id_edificio',
    ];
    //  Define la relación belongsTo donde un Piso pertenece a un único Edificio.
    public function edificios()
    {
        return $this->belongsTo(Edificio::class, 'EDIFICIO_id_edificio', 'id_edificio');
    }
    public function ambientes()
    {
        return $this->hasMany(Ambiente::class, 'PISO_id_piso', 'id_piso');
    }
}
