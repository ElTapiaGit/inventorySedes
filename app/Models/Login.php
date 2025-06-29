<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    use HasFactory;

    protected $table = 'login';

    protected $primaryKey = 'id_acceso'; // Clave primaria de la tabla
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = [
        'nombre',
        'contrasena',
        'PERSONAL_id_personal',
    ];

    //para defininr relacion de 1 a 1 con la tabla personal
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }
}
