<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UsoAmbiente;
use App\Models\Prestamo;
use App\Models\Mantenimiento;
use App\Models\Usuario;

class PrincipalController extends Controller
{
    public function principal()
    {
        $edificioId = Auth::user()->edificio_activo_id;

        try {
            $ambientesEnUso = UsoAmbiente::whereDoesntHave('finalUsos')
                ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId);
                })
                ->count();
        } catch (\Exception $e) {
            $ambientesEnUso = 0;
        }

        try {
            $prestamosPendientes = Prestamo::whereDoesntHave('devolucion')
                ->whereHas('personal', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId)
                      ->orWhereHas('edificiosAsignados', function ($sub) use ($edificioId) {
                          $sub->where('edificio.id_edificio', $edificioId);
                      });
                })
                ->count();
        } catch (\Exception $e) {
            $prestamosPendientes = 0;
        }

        try {
            $mantenimientosEnProceso = Mantenimiento::whereDoesntHave('finalMantenimiento')
                ->whereHas('personal', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId)
                      ->orWhereHas('edificiosAsignados', function ($sub) use ($edificioId) {
                          $sub->where('edificio.id_edificio', $edificioId);
                      });
                })
                ->count();
        } catch (\Exception $e) {
            $mantenimientosEnProceso = 0;
        }

        try {
            $totalUsuarios = Usuario::count();
        } catch (\Exception $e) {
            $totalUsuarios = 0;
        }

        return view('encargados.index', compact(
            'ambientesEnUso',
            'prestamosPendientes',
            'mantenimientosEnProceso',
            'totalUsuarios'
        ));
    }

    public function seleccionarEdificio($id)
    {
        $idInt = (int) $id;
        $permitidos = Auth::user()->edificiosPermitidosIds();

        if (!in_array($idInt, $permitidos, true)) {
            abort(403, 'Acceso denegado a este edificio.');
        }

        session(['encargado_edificio_id' => $idInt]);

        $prevUrl = url()->previous();
        $prevPath = parse_url($prevUrl, PHP_URL_PATH) ?? '';
        $prevPathLower = mb_strtolower($prevPath);

        // Si viene de un listado principal, redirigir a ese mismo listado
        if (str_contains($prevPathLower, '/encargado/inventario') && !preg_match('#/inventario/[^/]+/[^/]+#', $prevPathLower)) {
            return redirect()->route('encargado.inventario.index');
        }

        if (str_contains($prevPathLower, '/encargado/prestamos') && !preg_match('#/prestamos/\d+#', $prevPathLower)) {
            return redirect()->route('encargado.prestamo');
        }

        if (str_contains($prevPathLower, '/encargado/mantenimientos') && !preg_match('#/mantenimientos/\d+#', $prevPathLower)) {
            return redirect()->route('encargado.mantenimiento');
        }

        if (str_contains($prevPathLower, '/encargado/ambientes') && !str_contains($prevPathLower, '/detalles')) {
            return redirect()->route('movimiento.ambiente.index');
        }

        if (str_contains($prevPathLower, '/encargado/usuarios')) {
            return redirect()->route('encargado.usuarios');
        }

        if (str_contains($prevPathLower, '/encargado/principal')) {
            return redirect()->route('encargado.inicio');
        }

        // Si estaba en una creacion de articulos, volver a la misma vista de creacion con el nuevo edificio
        if (str_contains($prevPathLower, '/encargado/registro/equipo')) {
            return redirect()->route('equipo.create');
        }
        if (str_contains($prevPathLower, '/encargado/material/crear')) {
            return redirect()->route('material.create');
        }
        if (str_contains($prevPathLower, '/encargado/mobiliario/create')) {
            return redirect()->route('mobiliario.create');
        }
        if (str_contains($prevPathLower, '/encargado/accesorio/create')) {
            return redirect()->route('accesorio.create');
        }

        // Si estaba en un detalle especifico (articulo, prestamo, mantenimiento, uso), volver al inicio para evitar 403/404
        return redirect()->route('encargado.inicio');
    }
}
