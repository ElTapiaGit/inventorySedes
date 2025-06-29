<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = ['nombre', 'apellidos', 'celular', 'TIPO_USUARIO_id_tipo_usu'];

    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'TIPO_USUARIO_id_tipo_usu');
    }

    // Método para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellidos;
    }
}
