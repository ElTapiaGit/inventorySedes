<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;
    //Especificamos el nombre de la tabla sin la 's'
    protected $table = 'sede';

    protected $primaryKey = 'id_sede';
    public $timestamps = false;

    protected $fillable = ['nombre']; // Incluye solo los campos que deseas permitir
    
    //para las llaves foraneas
    public function edificios()
    {
        return $this->hasMany(Edificio::class, 'SEDE_id_sede');
    }
}
