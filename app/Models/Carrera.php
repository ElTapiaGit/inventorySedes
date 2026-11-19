<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'carrera';

    protected $primaryKey = 'id_carrera';
    public $timestamps = false;

    protected $fillable = [
        'nombre_carrera',
        'codigo',
        'logo',
    ];

    public function edificios()
    {
        return $this->hasMany(Edificio::class, 'CARRERA_id_carrera', 'id_carrera');
    }

    public function ambientes()
    {
        return $this->hasMany(Ambiente::class, 'CARRERA_id_carrera', 'id_carrera');
    }

    public function personal()
    {
        return $this->hasMany(Personal::class, 'CARRERA_id_carrera', 'id_carrera');
    }

    public function personals()
    {
        return $this->personal();
    }
}
