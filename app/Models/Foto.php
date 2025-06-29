<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    protected $table = 'foto';
    protected $primaryKey = 'id_foto';
    public $timestamps = false;

    protected $fillable = [
        'id_foto',
        'ruta_foto'
    ];

    // Relaciones
    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'FOTO_id_foto');
    }

    public function mobiliarios()
    {
        return $this->hasMany(Mobiliario::class, 'FOTO_id_foto');
    }

    public function materiales()
    {
        return $this->hasMany(Material::class, 'FOTO_id_foto');
    }
    public function accesorios()
    {
        return $this->hasMany(Accesorio::class, 'FOTO_id_foto');
    }
    //metodo para registrar foto de accesorio
    public function accesorio()
    {
        return $this->hasOne(Accesorio::class, 'FOTO_id_foto');
    }
}
