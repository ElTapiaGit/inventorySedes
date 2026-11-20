<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Support\Str;


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
        'EDIFICIO_id_edificio',
        'CARRERA_id_carrera'
    ];

    // Relación con el modelo Carrera
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'CARRERA_id_carrera', 'id_carrera');
    }

    // Comprueba si el personal tiene rol de coordinador
    public function esCoordinador(): bool
    {
        $rol = $this->tipoPersonal->descripcion_per ?? '';
        return Str::startsWith(Str::lower(trim($rol)), 'coordinador');
    }

    // Obtiene el nombre de la carrera coordinada (priorizando relacion, con fallback al texto del rol)
    public function getCarreraCoordinadaAttribute(): ?string
    {
        if ($this->relationLoaded('carrera') && $this->carrera) {
            return $this->carrera->nombre_carrera;
        }

        if ($this->carrera) {
            return $this->carrera->nombre_carrera;
        }

        $rol = trim($this->tipoPersonal->descripcion_per ?? '');
        if (preg_match('/^coordinador(?:\s+de)?\s+(.+)$/i', $rol, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    // Obtiene el ID de la carrera coordinada (priorizando columna FK, con fallback por nombre en Carrera)
    public function getIdCarreraCoordinadaAttribute(): ?int
    {
        if (!is_null($this->CARRERA_id_carrera)) {
            return (int) $this->CARRERA_id_carrera;
        }

        $nombreCarrera = $this->carrera_coordinada;
        if ($nombreCarrera) {
            $carrera = Carrera::where('nombre_carrera', $nombreCarrera)->first();
            return $carrera ? (int) $carrera->id_carrera : null;
        }

        return null;
    }


    protected ?Edificio $memoEdificioActivo = null;

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

    // Relación con edificios asignados mediante la tabla pivote personal_edificio
    public function edificiosAsignados()
    {
        return $this->belongsToMany(
            Edificio::class,
            'personal_edificio',
            'PERSONAL_id_personal',
            'EDIFICIO_id_edificio'
        )->withPivot('es_principal');
    }

    // Retorna array de enteros con los IDs de edificios permitidos
    public function edificiosPermitidosIds(): array
    {
        if ($this->relationLoaded('edificiosAsignados')) {
            $ids = $this->edificiosAsignados->pluck('id_edificio')->toArray();
        } else {
            $ids = $this->edificiosAsignados()->pluck('edificio.id_edificio')->toArray();
        }

        if (empty($ids) && !is_null($this->EDIFICIO_id_edificio)) {
            $ids = [(int) $this->EDIFICIO_id_edificio];
        }

        return array_map('intval', $ids);
    }

    // Accesor del ID del edificio activo en sesión con blindaje IDOR
    public function getEdificioActivoIdAttribute(): int
    {
        $permitidos = $this->edificiosPermitidosIds();
        $sessionEdificioId = session('encargado_edificio_id');

        if ($sessionEdificioId !== null && in_array((int) $sessionEdificioId, $permitidos, true)) {
            return (int) $sessionEdificioId;
        }

        return (int) ($this->EDIFICIO_id_edificio ?? ($permitidos[0] ?? 0));
    }

    // Accesor del modelo Edificio activo con memoización ligera
    public function getEdificioActivoAttribute(): ?Edificio
    {
        $activoId = $this->edificio_activo_id;

        if ($this->memoEdificioActivo && $this->memoEdificioActivo->id_edificio === $activoId) {
            return $this->memoEdificioActivo;
        }

        if ($this->relationLoaded('edificiosAsignados')) {
            $encontrado = $this->edificiosAsignados->firstWhere('id_edificio', $activoId);
            if ($encontrado) {
                $this->memoEdificioActivo = $encontrado;
                return $this->memoEdificioActivo;
            }
        }

        if ($this->relationLoaded('edificio') && $this->edificio && $this->edificio->id_edificio === $activoId) {
            $this->memoEdificioActivo = $this->edificio;
            return $this->memoEdificioActivo;
        }

        $this->memoEdificioActivo = Edificio::with('sede')->find($activoId);
        return $this->memoEdificioActivo;
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
