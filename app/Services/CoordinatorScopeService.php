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
     * Cache en memoria por request para evitar consultas SQL redundantes.
     */
    protected array $memoCarreraId = [];
    protected array $memoEdificios = [];
    protected array $memoEdificiosIds = [];
    protected array $memoAmbientesIds = [];
    protected array $memoActivoId = [];
    protected array $memoEdificioActivo = [];

    /**
     * Limpia la cache en memoria de la instancia.
     */
    public function resetMemoization(): void
    {
        $this->memoCarreraId = [];
        $this->memoEdificios = [];
        $this->memoEdificiosIds = [];
        $this->memoAmbientesIds = [];
        $this->memoActivoId = [];
        $this->memoEdificioActivo = [];
    }

    /**
     * Resuelve la clave de usuario para la cache en memoria.
     */
    protected function getUserKey(?Personal $user): string
    {
        if ($user && $user->id_personal) {
            return (string) $user->id_personal;
        }
        $authId = Auth::id();
        return $authId ? (string) $authId : 'guest';
    }

    /**
     * Obtiene el ID de la carrera coordinada por el usuario.
     */
    public function getCarreraId(?Personal $user = null): ?int
    {
        $userKey = $this->getUserKey($user);
        if (array_key_exists($userKey, $this->memoCarreraId)) {
            return $this->memoCarreraId[$userKey];
        }

        $user = $user ?? Auth::user();
        if (!$user) {
            return $this->memoCarreraId[$userKey] = null;
        }

        return $this->memoCarreraId[$userKey] = $user->id_carrera_coordinada;
    }

    /**
     * Devuelve la coleccion de edificios permitidos para el coordinador en su sede.
     */
    public function getEdificiosPermitidos(?Personal $user = null): Collection
    {
        $userKey = $this->getUserKey($user);
        if (isset($this->memoEdificios[$userKey])) {
            return $this->memoEdificios[$userKey];
        }

        $user = $user ?? Auth::user();
        if (!$user) {
            return $this->memoEdificios[$userKey] = new Collection();
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

        return $this->memoEdificios[$userKey] = $query->orderBy('id_edificio')->get();
    }

    /**
     * Devuelve los IDs de los edificios permitidos.
     */
    public function getEdificiosPermitidosIds(?Personal $user = null): array
    {
        $userKey = $this->getUserKey($user);
        if (isset($this->memoEdificiosIds[$userKey])) {
            return $this->memoEdificiosIds[$userKey];
        }

        return $this->memoEdificiosIds[$userKey] = $this->getEdificiosPermitidos($user)
            ->pluck('id_edificio')
            ->map(fn($id) => (int)$id)
            ->toArray();
    }

    /**
     * Determina el ID del edificio activo en sesion.
     */
    public function getEdificioActivoId(?Personal $user = null): int
    {
        $userKey = $this->getUserKey($user);
        if (isset($this->memoActivoId[$userKey])) {
            return $this->memoActivoId[$userKey];
        }

        $user = $user ?? Auth::user();
        $permitidos = $this->getEdificiosPermitidosIds($user);

        if (empty($permitidos)) {
            return $this->memoActivoId[$userKey] = 0;
        }

        $sessionEdificioId = session('coordinator_edificio_id');
        if ($sessionEdificioId && in_array((int) $sessionEdificioId, $permitidos, true)) {
            return $this->memoActivoId[$userKey] = (int) $sessionEdificioId;
        }

        // Priorizar el edificio asignado al usuario si esta permitido
        if ($user && $user->EDIFICIO_id_edificio && in_array((int) $user->EDIFICIO_id_edificio, $permitidos, true)) {
            session(['coordinator_edificio_id' => (int) $user->EDIFICIO_id_edificio]);
            return $this->memoActivoId[$userKey] = (int) $user->EDIFICIO_id_edificio;
        }

        session(['coordinator_edificio_id' => $permitidos[0]]);
        return $this->memoActivoId[$userKey] = $permitidos[0];
    }

    /**
     * Devuelve el modelo del edificio activo.
     */
    public function getEdificioActivo(?Personal $user = null): ?Edificio
    {
        $userKey = $this->getUserKey($user);
        if (array_key_exists($userKey, $this->memoEdificioActivo)) {
            return $this->memoEdificioActivo[$userKey];
        }

        $id = $this->getEdificioActivoId($user);
        return $this->memoEdificioActivo[$userKey] = ($id > 0 ? Edificio::find($id) : null);
    }

    /**
     * Resuelve los IDs de los ambientes que el coordinador puede ver dentro de uno o todos sus edificios.
     */
    public function getAmbientesPermitidosIds(?int $edificioId = null, ?Personal $user = null): array
    {
        $userKey = $this->getUserKey($user);
        $cacheKey = $edificioId !== null ? (string)$edificioId : 'all';

        if (isset($this->memoAmbientesIds[$userKey][$cacheKey])) {
            return $this->memoAmbientesIds[$userKey][$cacheKey];
        }

        $user = $user ?? Auth::user();
        if (!$user) {
            return $this->memoAmbientesIds[$userKey][$cacheKey] = [];
        }

        $carreraId = $this->getCarreraId($user);
        $permitidosEdificios = $this->getEdificiosPermitidos($user);

        if ($edificioId !== null) {
            $targetEdificio = $permitidosEdificios->firstWhere('id_edificio', $edificioId);
            if (!$targetEdificio) {
                return $this->memoAmbientesIds[$userKey][$cacheKey] = [];
            }
            return $this->memoAmbientesIds[$userKey][$cacheKey] = $this->resolverAmbientesDeEdificio($targetEdificio, $carreraId);
        }

        $todosAmbientes = [];
        foreach ($permitidosEdificios as $edificio) {
            $ambientesEdificio = $this->resolverAmbientesDeEdificio($edificio, $carreraId);
            $todosAmbientes = array_merge($todosAmbientes, $ambientesEdificio);
        }

        return $this->memoAmbientesIds[$userKey][$cacheKey] = array_values(array_unique($todosAmbientes));
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
