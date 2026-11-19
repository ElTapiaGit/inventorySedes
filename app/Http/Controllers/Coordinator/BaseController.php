<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Edificio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    // Controlador base para compartir contexto dinamico de carrera y edificios
    public function __construct()
    {
        static::registerViewComposer();
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
                ]);
                return;
            }

            // 1. Resolver la carrera activa del coordinador autenticado
            $carreraModel = $user->carrera;
            if (!$carreraModel && !empty($user->id_carrera_coordinada)) {
                $carreraModel = Carrera::find($user->id_carrera_coordinada);
            }

            $carreraId = $carreraModel ? $carreraModel->id_carrera : $user->id_carrera_coordinada;

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

            // 3. Edificios disponibles para el coordinador
            $edificiosQuery = Edificio::query();

            if ($carreraId) {
                $edificiosQuery->where(function ($q) use ($carreraId) {
                    $q->where('CARRERA_id_carrera', $carreraId)
                      ->orWhereHas('pisos.ambientes', function ($aq) use ($carreraId) {
                          $aq->where('CARRERA_id_carrera', $carreraId);
                      });
                });

                $sedeId = $user->edificio->SEDE_id_sede ?? null;
                if ($sedeId) {
                    $edificiosQuery->where('SEDE_id_sede', $sedeId);
                }
            } elseif ($user->edificio && $user->edificio->SEDE_id_sede) {
                $edificiosQuery->where('SEDE_id_sede', $user->edificio->SEDE_id_sede);
            }

            $edificios = $edificiosQuery->get();

            $view->with([
                'coordCarreraNombre' => $coordCarreraNombre,
                'coordCarreraTitulo' => $coordCarreraTitulo,
                'coordLogoUrl' => $coordLogoUrl,
                'edificios' => $edificios,
            ]);
        });
    }
}
