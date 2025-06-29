<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;


class Personal extends Model implements AuthenticatableContract
{
    use AuthenticatableTrait;

    use HasFactory;
    //Especificamos el nombre de la tabla sin la 's'
    protected $table = 'personal';

    protected $primaryKey = 'id_personal'; // Clave primaria de la tabla
    public $timestamps = false; // Desactivar los timestamps si no se usan

    // Campos que se pueden llenar de forma masiva
    protected $fillable = [
        'ap_paterno',
        'ap_materno',
        'nombre',
        'celular',
        'estado',
        'TIPO_PERSONAL_id_tipo_per',
        'EDIFICIO_id_edificio'
    ];


    // Relación con el modelo TipoPersonal
    public function tipoPersonal()
    {
        return $this->belongsTo(TipoPersonal::class, 'TIPO_PERSONAL_id_tipo_per', 'id_tipo_per');
    }
    // Relación con el modelo Edificio
    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'EDIFICIO_id_edificio', 'id_edificio');
    }
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'PERSONAL_id_personal');
    }

    //define la relacion de 1 a 1 con la tabla login
    public function login()
    {
        return $this->hasOne(Login::class, 'PERSONAL_id_personal');
    }

    //metodo para mostrar nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre. ' ' . $this->ap_paterno . ' ' . $this->ap_materno;
    }
    //relacion con descartes
    public function descartes()
    {
        return $this->hasMany(Descarte::class, 'PERSONAL_id_personal');
    }

    // Relación con el modelo `Mantenimiento`
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'PERSONAL_id_personal');
    }
}
