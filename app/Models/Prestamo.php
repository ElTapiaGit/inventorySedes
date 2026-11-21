<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamo';
    protected $primaryKey = 'id_prestamo';
    public $timestamps = false;


    protected $fillable = [
        'nombre_solicitante',
        'descripcion_prestamo',
        'fch_prestamo',
        'hora_prestamo',
        'PERSONAL_id_personal',
    ];

    //relaciones
    public function detallePrestamos()
    {
        return $this->hasMany(DetallePrestamo::class, 'PRESTAMO_id_prestamo');
    }

    public function detallesPrestamos()
    {
        return $this->detallePrestamos();
    }

    public function devolucion()
    {
        //return $this->hasMany(Devolucion::class, 'PRESTAMO_id_prestamo');
        return $this->hasOne(Devolucion::class, 'PRESTAMO_id_prestamo');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }

    /**
     * Scope para filtrar prestamos segun la pertenencia territorial de sus articulos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|array $edificioId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorEdificio($query, int|array $edificioId)
    {
        $ids = is_array($edificioId) ? array_map('intval', $edificioId) : [(int) $edificioId];

        return $query->where(function ($mainQuery) use ($ids) {
            $mainQuery->whereHas('detallePrestamos', function ($qDetalle) use ($ids) {
                $qDetalle->where(function ($qArticulo) use ($ids) {
                    $qArticulo->whereHas('equipo.ambiente.piso', function ($q) use ($ids) {
                        $q->whereIn('EDIFICIO_id_edificio', $ids);
                    })
                    ->orWhereHas('mobiliario.ambiente.piso', function ($q) use ($ids) {
                        $q->whereIn('EDIFICIO_id_edificio', $ids);
                    })
                    ->orWhereHas('material.ambiente.piso', function ($q) use ($ids) {
                        $q->whereIn('EDIFICIO_id_edificio', $ids);
                    })
                    ->orWhereHas('accesorio.equipos.ambiente.piso', function ($q) use ($ids) {
                        $q->whereIn('EDIFICIO_id_edificio', $ids);
                    });
                });
            })
            ->orWhere(function ($fallback) use ($ids) {
                $fallback->doesntHave('detallePrestamos')
                    ->whereHas('personal', function ($qPer) use ($ids) {
                        $qPer->whereIn('EDIFICIO_id_edificio', $ids);
                    });
            });
        });
    }
}
