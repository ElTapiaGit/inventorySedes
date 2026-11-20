<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Edificio;
use App\Services\CoordinatorScopeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    public function __construct()
    {
        static::registerViewComposer();
    }

    // Cambia el edificio activo en la sesion del coordinador con redireccion inteligente
    public function seleccionarEdificio($id, CoordinatorScopeService $scopeService)
    {
        $scopeService->validarAccesoEdificio((int) $id);
        session(['coordinator_edificio_id' => (int) $id]);
        $scopeService->resetMemoization();

        $edificio = Edificio::find((int) $id);
        $isTargetClinica = $edificio && str_contains(mb_strtolower($edificio->nombre_edi), 'clinica');

        // Redireccion inteligente segun el modulo en el que se encontraba el usuario
        $prevUrl = url()->previous();
        $prevPath = parse_url($prevUrl, PHP_URL_PATH) ?? '';
        $prevPathLower = mb_strtolower($prevPath);

        // Si estaba en historial de mantenimiento (excluyendo detalle numerico)
        if (str_contains($prevPathLower, 'historial-mantenimiento') && !preg_match('#historial-mantenimiento/\w+#', $prevPathLower)) {
            return redirect()->route($isTargetClinica ? 'clinica.historialmantenimientos.index' : 'historialmantenimientos.index');
        }

        // Si estaba en mantenimientos (excluyendo detalle)
        if (str_contains($prevPathLower, 'mantenimiento') && !str_contains($prevPathLower, 'detalles')) {
            return redirect()->route($isTargetClinica ? 'clinica.mantenimiento.index' : 'mantenimiento.index');
        }

        // Si estaba en prestamos (excluyendo detalle)
        if (str_contains($prevPathLower, 'prestamo') && !str_contains($prevPathLower, 'detalles')) {
            return redirect()->route($isTargetClinica ? 'clinica.prestamos.index' : 'prestamos.index');
        }

        // Si estaba en movimientos (excluyendo detalle)
        if (str_contains($prevPathLower, 'movimiento') && !str_contains($prevPathLower, 'detalles')) {
            return redirect()->route($isTargetClinica ? 'clinica.movimientos.index' : 'movimientos.index');
        }

        // Si estaba en accesorios (excluyendo detalle y fotos)
        if (str_contains($prevPathLower, 'accesorio') && !str_contains($prevPathLower, 'detalles') && !str_contains($prevPathLower, 'foto')) {
            return redirect()->route($isTargetClinica ? 'clinica.accesorios.index' : 'accesorios.index');
        }

        // Si estaba en laboratorios / ambientes de edificio (excluyendo fichas de ambiente especifico)
        if (str_contains($prevPathLower, 'laboratorio') || (str_contains($prevPathLower, 'ambientes') && !str_contains($prevPathLower, 'contenido') && !str_contains($prevPathLower, 'equipos') && !str_contains($prevPathLower, 'material') && !str_contains($prevPathLower, 'mobiliario'))) {
            return redirect()->route($isTargetClinica ? 'coordinator.clinica.ambientes' : 'laboratorios.index');
        }

        // Si estaba en reportes
        if (str_contains($prevPathLower, 'reporte')) {
            return redirect()->route($isTargetClinica ? 'reporte.clinica.index' : 'reporte.index');
        }

        // Por defecto o si estaba en inicio / ficha de detalle
        return redirect()->route($isTargetClinica ? 'coordinator.clinica.inicio' : 'coordinator.inicio');
    }

    // Registra el View Composer exclusivo para las vistas de coordinador y sus layouts
    public static function registerViewComposer(): void
    {
        View::composer([
            'layouts.coordinator',
            'coordinator.*',
        ], function ($view) {
            $user = Auth::user();

            if (!$user) {
                $view->with([
                    'coordCarreraNombre' => 'Coordinacion Academica',
                    'coordCarreraTitulo' => 'Coordinacion Academica',
                    'coordLogoUrl' => asset('img/logo.webp'),
                    'coordFondoUrl' => null,
                    'edificios' => collect(),
                    'edificioActivo' => null,
                    'edificioActivoId' => 0,
                ]);
                return;
            }

            $scopeService = app(CoordinatorScopeService::class);

            // 1. Resolver la carrera activa del coordinador autenticado
            $carreraModel = $user->carrera;
            if (!$carreraModel && !empty($user->id_carrera_coordinada)) {
                $carreraModel = Carrera::find($user->id_carrera_coordinada);
            }

            // 2. Identidad visual inyectada a las vistas de coordinador
            if ($carreraModel && !empty($carreraModel->nombre_carrera)) {
                $coordCarreraNombre = $carreraModel->nombre_carrera;
            } elseif (!empty($user->carrera_coordinada)) {
                $coordCarreraNombre = $user->carrera_coordinada;
            } else {
                $coordCarreraNombre = 'Coordinacion Academica';
            }

            if ($coordCarreraNombre !== 'Coordinacion Academica') {
                $coordCarreraTitulo = 'Carrera de ' . $coordCarreraNombre;
            } else {
                $coordCarreraTitulo = 'Coordinacion Academica';
            }

            if ($carreraModel && !empty($carreraModel->logo)) {
                if (file_exists(public_path($carreraModel->logo))) {
                    $coordLogoUrl = asset($carreraModel->logo);
                } elseif (Storage::disk('public')->exists($carreraModel->logo)) {
                    $coordLogoUrl = asset('storage/' . $carreraModel->logo);
                } else {
                    $coordLogoUrl = asset('img/logo.webp');
                }
            } else {
                $coordLogoUrl = asset('img/logo.webp');
            }

            // 3. Fondo dinamico por carrera
            $carreraId = $scopeService->getCarreraId($user);
            $carreraLower = mb_strtolower($coordCarreraNombre);

            if (($carreraId === 1 || str_contains($carreraLower, 'odontolog')) && file_exists(public_path('img/coordinator/fondo_odonot.webp'))) {
                $coordFondoUrl = asset('img/coordinator/fondo_odonot.webp');
            } elseif (file_exists(public_path('img/coordinator/fondo-coordincion.webp'))) {
                $coordFondoUrl = asset('img/coordinator/fondo-coordincion.webp');
            } else {
                $coordFondoUrl = null;
            }

            // 4. Edificios y edificio activo
            $edificios = $scopeService->getEdificiosPermitidos($user);
            $edificioActivoId = $scopeService->getEdificioActivoId($user);
            $edificioActivo = $edificios->firstWhere('id_edificio', $edificioActivoId);

            $view->with([
                'coordCarreraNombre' => $coordCarreraNombre,
                'coordCarreraTitulo' => $coordCarreraTitulo,
                'coordLogoUrl' => $coordLogoUrl,
                'coordFondoUrl' => $coordFondoUrl,
                'edificios' => $edificios,
                'edificioActivo' => $edificioActivo,
                'edificioActivoId' => $edificioActivoId,
            ]);
        });
    }
}
