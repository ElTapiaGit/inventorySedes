<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;
use App\Models\Devolucion;
use Illuminate\Support\Facades\Crypt;


class DetallesPrestAdminController extends Controller
{
    //
    public function show($id_prestamo)
    {
        try {
            // Desencriptar el ID del préstamo
            try {
                $id_prestamo = Crypt::decrypt($id_prestamo);
            } catch (\Exception $e) {
                return redirect()->route('prestamosadmin.index')->withErrors('ID de ambiente no válido.');
            }

            // Obtener el préstamo y sus detalles
            $prestamo = Prestamo::with('personal', 'devolucion')
            ->where('id_prestamo', $id_prestamo)
            ->firstOrFail();

            // Obtener los detalles del préstamo
            $detalle_prestamos = DetallePrestamo::where('PRESTAMO_id_prestamo', $id_prestamo)->get();
            

            // Obtener detalles de la devolución, si existe
            $devolucion = Devolucion::where('PRESTAMO_id_prestamo', $id_prestamo)->first();

            // Agregar detalles de devolución al objeto préstamo
            if ($devolucion) {
                $prestamo->fch_devolucion = $devolucion->fch_devolucion;
                $prestamo->hora_devolucion = $devolucion->hora_devolucion;
                $prestamo->descripcion_devolucion = $devolucion->descripcion_devolucion;
                $prestamo->nombre_encargados = $devolucion->personal ? $devolucion->personal->nombre_completo : 'No asignado';
            } else {
                $prestamo->fch_devolucion = null;
                $prestamo->hora_devolucion = null;
                $prestamo->descripcion_devolucion = null;
                $prestamo->nombre_encargados = 'No asignado';
            }

            // Obtener detalles de los artículos prestados
            $detalles_prestamos = $prestamo->detallePrestamos->map(function ($detalle) {
                return [
                    'cod_equipo' => $detalle->cod_articulo,
                    'nombre_equipo' => $detalle->equipo->nombre_equi ?? 
                                    $detalle->mobiliario->tipoMobiliario->tipo_mueble ?? 
                                    $detalle->accesorio->nombre_acce ?? 
                                    $detalle->material->tipo_mate ?? 
                                    'Desconocido equipo',
                    'observacion_detalle' => $detalle->observacion_detalle,
                    'estado_equipo' => $detalle->equipo->estado_equi ?? 
                                    $detalle->mobiliario->estado_mueb ?? 
                                    $detalle->accesorio->estado_acce ?? 
                                    $detalle->material->estado_mate ?? 
                                    'Desconocido estado',
                    'ambiente_equipo' => $detalle->equipo->ambiente->nombre ?? 
                                        $detalle->mobiliario->ambiente->nombre ?? 
                                        $detalle->accesorio->ambiente->nombre ?? 
                                        $detalle->material->ambiente->nombre ?? 
                                        'Desconocido ambiente'
                ];
            });


            return view('Administrator.detallesPrestamoAdmin', compact('prestamo', 'detalle_prestamos', 'detalles_prestamos'));

        } catch (\Exception $e) {
            return redirect()->route('prestamosAdmin.index')->with('errordata', 'Error al obtener los detalles del préstamo: ');
        }
    }
}
