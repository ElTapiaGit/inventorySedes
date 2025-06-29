<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    use HasFactory;

    protected $table = 'tipo_usuario';
    protected $primaryKey = 'id_tipo_usu';
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = ['tipo'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'TIPO_USUARIO_id_tipo_usu');
    }
}
