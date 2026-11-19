<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class DetallesEquipoController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function index($encryptedId)
    {
        try {
            try {
                $cod_equipo = Crypt::decrypt($encryptedId);
            } catch (DecryptException $e) {
                abort(404, 'Equipo no encontrado');
            }

            $equipo = DB::table('equipo')
                ->leftJoin('foto', 'equipo.FOTO_id_foto', '=', 'foto.id_foto')
                ->leftJoin('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('equipo.cod_equipo', $cod_equipo)
                ->select(
                    'foto.ruta_foto',
                    'equipo.cod_equipo',
                    'equipo.nombre_equi',
                    'equipo.marca',
                    'equipo.modelo',
                    'equipo.descripcion_equi',
                    'equipo.empotrado',
                    'equipo.estado_equi',
                    'equipo.observaciones_equi',
                    'equipo.vida_util',
                    'equipo.fch_registro',
                    'equipo.AMBIENTE_id_ambiente',
                    'ambiente.nombre'
                )
                ->first();

            if (!$equipo) {
                abort(404, 'Equipo no encontrado');
            }

            // Blindaje IDOR: verificar pertenencia del ambiente al coordinador
            $this->scopeService->validarAccesoAmbiente((int) $equipo->AMBIENTE_id_ambiente);

            $componentes = DB::table('componente')
                ->join('equipo', 'componente.EQUIPO_cod_equipo', '=', 'equipo.cod_equipo')
                ->where('componente.EQUIPO_cod_equipo', $cod_equipo)
                ->select('componente.nombre_compo', 'componente.descripcion_compo', 'componente.estado_compo')
                ->get();

            $accesorios = DB::table('accesorio')
                ->join('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
                ->join('equipo', 'equipo.cod_equipo', '=', 'equipo_has_accesorio.EQUIPO_cod_equipo')
                ->leftJoin('foto', 'accesorio.FOTO_id_foto', '=', 'foto.id_foto')
                ->where('equipo.cod_equipo', $cod_equipo)
                ->select(
                    'accesorio.cod_accesorio',
                    'accesorio.nombre_acce',
                    'accesorio.descripcion_acce',
                    'accesorio.observacion_ace',
                    'accesorio.estado_acce',
                    'accesorio.vida_util',
                    'accesorio.ubicacion',
                    'accesorio.fch_registro_acce',
                    'accesorio.FOTO_id_foto',
                    'foto.ruta_foto'
                )
                ->get();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesEquipo', [
                'equipo' => $equipo,
                'componentes' => $componentes,
                'accesorios' => $accesorios,
                'edificios' => $edificios,
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del equipo.');
        }
    }

    public function fotoaccesorioEdiCen(Request $request)
    {
        try {
            $id_foto = $request->input('cod_accesorio');
            $fotos = DB::table('foto')
                ->where('id_foto', $id_foto)
                ->select('foto.ruta_foto')
                ->first();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.fotoAccess', compact('fotos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener la foto del accesorio.');
        }
    }

    public function detalleEquipo($encryptedId)
    {
        try {
            try {
                $cod_equipo = Crypt::decrypt($encryptedId);
            } catch (DecryptException $e) {
                abort(404, 'Equipo no encontrado');
            }

            $equipo = DB::table('equipo')
                ->leftJoin('foto', 'equipo.FOTO_id_foto', '=', 'foto.id_foto')
                ->leftJoin('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('equipo.cod_equipo', $cod_equipo)
                ->select(
                    'foto.ruta_foto',
                    'equipo.cod_equipo',
                    'equipo.nombre_equi',
                    'equipo.marca',
                    'equipo.modelo',
                    'equipo.descripcion_equi',
                    'equipo.empotrado',
                    'equipo.estado_equi',
                    'equipo.observaciones_equi',
                    'equipo.vida_util',
                    'equipo.fch_registro',
                    'equipo.AMBIENTE_id_ambiente',
                    'ambiente.nombre'
                )
                ->first();

            if (!$equipo) {
                abort(404, 'Equipo no encontrado');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente((int) $equipo->AMBIENTE_id_ambiente);

            $componentes = DB::table('componente')
                ->join('equipo', 'componente.EQUIPO_cod_equipo', '=', 'equipo.cod_equipo')
                ->where('componente.EQUIPO_cod_equipo', $cod_equipo)
                ->select('componente.nombre_compo', 'componente.descripcion_compo', 'componente.estado_compo')
                ->get();

            $accesorios = DB::table('accesorio')
                ->join('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
                ->join('equipo', 'equipo.cod_equipo', '=', 'equipo_has_accesorio.EQUIPO_cod_equipo')
                ->leftJoin('foto', 'accesorio.FOTO_id_foto', '=', 'foto.id_foto')
                ->where('equipo.cod_equipo', $cod_equipo)
                ->select(
                    'accesorio.cod_accesorio',
                    'accesorio.nombre_acce',
                    'accesorio.descripcion_acce',
                    'accesorio.observacion_ace',
                    'accesorio.estado_acce',
                    'accesorio.vida_util',
                    'accesorio.ubicacion',
                    'accesorio.fch_registro_acce',
                    'accesorio.FOTO_id_foto',
                    'foto.ruta_foto'
                )
                ->get();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesEquipo', [
                'equipo' => $equipo,
                'componentes' => $componentes,
                'accesorios' => $accesorios,
                'edificios' => $edificios,
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los detalles del equipo.');
        }
    }

    public function fotoaccesorio(Request $request)
    {
        try {
            $id_foto = $request->input('cod_accesorio');
            $fotos = DB::table('foto')
                ->where('id_foto', $id_foto)
                ->select('foto.ruta_foto')
                ->first();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.fotoAccess', compact('fotos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener la foto.');
        }
    }
}
