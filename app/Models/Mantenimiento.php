<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    // Definimos la tabla asociada al modelo
    protected $table = 'inicio_mantenimiento';
    protected $primaryKey = 'id_mantenimiento_ini';
    public $timestamps = false;

    // Definimos los campos que pueden ser asignados masivamente
    protected $fillable = [
        'informe_inicial',
        'fch_inicio',
        'TECNICO_id_tecnico',
        'PERSONAL_id_personal'
    ];

    // Definimos la relación con la tabla `detalles_mantenimiento`
    public function detalleMantenimiento()
    {
        return $this->hasMany(DetallesMantenimiento::class, 'INICIO_MANTENIMIENTO_id_mantenimiento_ini');
    }

    public function detallesMantenimiento()
    {
        return $this->detalleMantenimiento();
    }

    public function detallesMantenimientos()
    {
        return $this->detalleMantenimiento();
    }

    // Definimos la relación con la tabla `tecnico`
    public function tecnico()
    {
        return $this->belongsTo(Tecnico::class, 'TECNICO_id_tecnico');
    }

    // Definimos la relación con la tabla `personal`
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }

    // Definimos la relación con la tabla `final_mantenimiento`
    public function finalMantenimiento()
    {
        return $this->hasOne(FinalMantenimiento::class, 'INICIO_MANTENIMIENTO_id_mantenimiento_ini');
    }

    /**
     * Scope para filtrar mantenimientos segun la pertenencia territorial de sus articulos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|array $edificioId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorEdificio($query, int|array $edificioId)
    {
        $ids = is_array($edificioId) ? array_map('intval', $edificioId) : [(int) $edificioId];

        return $query->where(function ($mainQuery) use ($ids) {
            $mainQuery->whereHas('detalleMantenimiento', function ($qDetalle) use ($ids) {
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
                $fallback->doesntHave('detalleMantenimiento')
                    ->whereHas('personal', function ($qPer) use ($ids) {
                        $qPer->whereIn('EDIFICIO_id_edificio', $ids);
                    });
            });
        });
    }
}
