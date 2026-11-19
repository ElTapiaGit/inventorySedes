<?php

namespace App\Services;

use App\Models\Ambiente;
use App\Models\Edificio;
use App\Models\Personal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CoordinatorScopeService
{
    /**
     * Obtiene el ID de la carrera coordinada por el usuario.
     */
    public function getCarreraId(?Personal $user = null): ?int
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return null;
        }

        return $user->id_carrera_coordinada;
    }

    /**
     * Devuelve la coleccion de edificios permitidos para el coordinador en su sede.
     */
    public function getEdificiosPermitidos(?Personal $user = null): Collection
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return new Collection();
        }

        $carreraId = $this->getCarreraId($user);
        $sedeId = $user->edificio->SEDE_id_sede ?? null;

        $query = Edificio::query();

        if ($carreraId !== null) {
            $query->where(function ($q) use ($carreraId) {
                $q->where('CARRERA_id_carrera', $carreraId)
                  ->orWhereHas('pisos.ambientes', function ($aq) use ($carreraId) {
                      $aq->where('CARRERA_id_carrera', $carreraId);
                  });
            });

            if ($sedeId) {
                $query->where('SEDE_id_sede', $sedeId);
            }
        } elseif ($sedeId) {
            $query->where('SEDE_id_sede', $sedeId);
        }

        return $query->orderBy('id_edificio')->get();
    }

    /**
     * Devuelve los IDs de los edificios permitidos.
     */
    public function getEdificiosPermitidosIds(?Personal $user = null): array
    {
        return $this->getEdificiosPermitidos($user)->pluck('id_edificio')->map(fn($id) => (int)$id)->toArray();
    }

    /**
     * Determina el ID del edificio activo en sesion.
     */
    public function getEdificioActivoId(?Personal $user = null): int
    {
        $user = $user ?? Auth::user();
        $permitidos = $this->getEdificiosPermitidosIds($user);

        if (empty($permitidos)) {
            return 0;
        }

        $sessionEdificioId = session('coordinator_edificio_id');
        if ($sessionEdificioId && in_array((int) $sessionEdificioId, $permitidos, true)) {
            return (int) $sessionEdificioId;
        }

        // Priorizar el edificio asignado al usuario si esta permitido
        if ($user && $user->EDIFICIO_id_edificio && in_array((int) $user->EDIFICIO_id_edificio, $permitidos, true)) {
            session(['coordinator_edificio_id' => (int) $user->EDIFICIO_id_edificio]);
            return (int) $user->EDIFICIO_id_edificio;
        }

        session(['coordinator_edificio_id' => $permitidos[0]]);
        return $permitidos[0];
    }

    /**
     * Devuelve el modelo del edificio activo.
     */
    public function getEdificioActivo(?Personal $user = null): ?Edificio
    {
        $id = $this->getEdificioActivoId($user);
        return $id > 0 ? Edificio::find($id) : null;
    }

    /**
     * Resuelve los IDs de los ambientes que el coordinador puede ver dentro de uno o todos sus edificios.
     */
    public function getAmbientesPermitidosIds(?int $edificioId = null, ?Personal $user = null): array
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return [];
        }

        $carreraId = $this->getCarreraId($user);
        $permitidosEdificios = $this->getEdificiosPermitidos($user);

        if ($edificioId !== null) {
            $targetEdificio = $permitidosEdificios->firstWhere('id_edificio', $edificioId);
            if (!$targetEdificio) {
                return [];
            }
            return $this->resolverAmbientesDeEdificio($targetEdificio, $carreraId);
        }

        $todosAmbientes = [];
        foreach ($permitidosEdificios as $edificio) {
            $ambientesEdificio = $this->resolverAmbientesDeEdificio($edificio, $carreraId);
            $todosAmbientes = array_merge($todosAmbientes, $ambientesEdificio);
        }

        return array_values(array_unique($todosAmbientes));
    }

    /**
     * Aplica la regla territorial/academica sobre un edificio especifico.
     */
    protected function resolverAmbientesDeEdificio(Edificio $edificio, ?int $carreraId): array
    {
        $query = Ambiente::whereHas('piso', function ($pq) use ($edificio) {
            $pq->where('EDIFICIO_id_edificio', $edificio->id_edificio);
        });

        if ($carreraId !== null) {
            // Edificio dedicado: todos sus ambientes pertenecen a la carrera
            if ((int) $edificio->CARRERA_id_carrera === $carreraId) {
                return $query->pluck('id_ambiente')->map(fn($id) => (int)$id)->toArray();
            }

            // Edificio compartido: solo los ambientes explícitamente marcados para la carrera
            return $query->where('CARRERA_id_carrera', $carreraId)
                ->pluck('id_ambiente')
                ->map(fn($id) => (int)$id)
                ->toArray();
        }

        // Coordinador general: todos los ambientes del edificio
        return $query->pluck('id_ambiente')->map(fn($id) => (int)$id)->toArray();
    }

    /**
     * Valida si un ambiente pertenece al alcance permitido del coordinador.
     */
    public function validarAccesoAmbiente(int $ambienteId, ?Personal $user = null): void
    {
        $permitidos = $this->getAmbientesPermitidosIds(null, $user);
        if (!in_array($ambienteId, $permitidos, true)) {
            abort(403, 'Acceso denegado a este ambiente.');
        }
    }

    /**
     * Valida si un edificio pertenece al alcance permitido del coordinador.
     */
    public function validarAccesoEdificio(int $edificioId, ?Personal $user = null): void
    {
        $permitidos = $this->getEdificiosPermitidosIds($user);
        if (!in_array($edificioId, $permitidos, true)) {
            abort(403, 'Acceso denegado a este edificio.');
        }
    }
}
