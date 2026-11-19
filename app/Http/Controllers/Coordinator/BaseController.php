<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Edificio;
use App\Services\CoordinatorScopeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    // Controlador base para compartir contexto dinamico de carrera y edificios
    public function __construct()
    {
        static::registerViewComposer();
    }

    // Cambia el edificio activo en la sesion del coordinador
    public function seleccionarEdificio($id, CoordinatorScopeService $scopeService)
    {
        $scopeService->validarAccesoEdificio((int) $id);
        session(['coordinator_edificio_id' => (int) $id]);

        $edificio = Edificio::find((int) $id);
        if ($edificio && str_contains(mb_strtolower($edificio->nombre_edi), 'clinica')) {
            return redirect()->route('coordinator.clinica.inicio');
        }

        return redirect()->route('coordinator.inicio');
    }

    // Registra el View Composer exclusivo para las vistas de coordinador y sus layouts
    public static function registerViewComposer(): void
    {
        View::composer([
            'layouts.coordinator',
            'layouts.coodinacion',
            'layouts.pagClinica',
            'coordinator.*',
        ], function ($view) {
            $user = Auth::user();

            if (!$user) {
                $view->with([
                    'coordCarreraNombre' => 'Coordinacion Academica',
                    'coordCarreraTitulo' => 'Coordinacion Academica',
                    'coordLogoUrl' => asset('img/logo.webp'),
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

            if ($carreraModel && !empty($carreraModel->logo) && file_exists(public_path($carreraModel->logo))) {
                $coordLogoUrl = asset($carreraModel->logo);
            } else {
                $coordLogoUrl = asset('img/logo.webp');
            }

            // 3. Edificios y edificio activo
            $edificios = $scopeService->getEdificiosPermitidos($user);
            $edificioActivoId = $scopeService->getEdificioActivoId($user);
            $edificioActivo = $edificios->firstWhere('id_edificio', $edificioActivoId);

            $view->with([
                'coordCarreraNombre' => $coordCarreraNombre,
                'coordCarreraTitulo' => $coordCarreraTitulo,
                'coordLogoUrl' => $coordLogoUrl,
                'edificios' => $edificios,
                'edificioActivo' => $edificioActivo,
                'edificioActivoId' => $edificioActivoId,
            ]);
        });
    }
}
