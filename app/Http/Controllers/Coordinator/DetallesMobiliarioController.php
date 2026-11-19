<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class DetallesMobiliarioController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function show($token)
    {
        try {
            try {
                $mobiliarioId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                abort(404, 'Mobiliario no encontrado');
            }

            $mobiliario = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->leftJoin('foto', 'mobiliario.FOTO_id_foto', '=', 'foto.id_foto')
                ->join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('mobiliario.cod_mueble', $mobiliarioId)
                ->select(
                    'mobiliario.cod_mueble',
                    'mobiliario.AMBIENTE_id_ambiente',
                    'ambiente.nombre',
                    'tipo_mobiliario.tipo_mueble',
                    'mobiliario.descripticion_mueb',
                    'mobiliario.observacion',
                    'mobiliario.estado_mueb',
                    'mobiliario.vida_util',
                    'mobiliario.fch_registro',
                    'foto.ruta_foto'
                )
                ->first();

            if (!$mobiliario) {
                abort(404, 'Mobiliario no encontrado');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente((int) $mobiliario->AMBIENTE_id_ambiente);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesMobiliario', [
                'mobiliario' => $mobiliario,
                'edificios' => $edificios
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del mobiliario.');
        }
    }

    public function detalleMobiliario($token)
    {
        try {
            try {
                $mobiliarioId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                abort(404, 'Mobiliario no encontrado');
            }

            $mobiliario = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->leftJoin('foto', 'mobiliario.FOTO_id_foto', '=', 'foto.id_foto')
                ->join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('mobiliario.cod_mueble', $mobiliarioId)
                ->select(
                    'mobiliario.cod_mueble',
                    'mobiliario.AMBIENTE_id_ambiente',
                    'ambiente.nombre',
                    'tipo_mobiliario.tipo_mueble',
                    'mobiliario.descripticion_mueb',
                    'mobiliario.observacion',
                    'mobiliario.estado_mueb',
                    'mobiliario.vida_util',
                    'mobiliario.fch_registro',
                    'foto.ruta_foto'
                )
                ->first();

            if (!$mobiliario) {
                abort(404, 'Mobiliario no encontrado');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente((int) $mobiliario->AMBIENTE_id_ambiente);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesMobiliario', [
                'mobiliario' => $mobiliario,
                'edificios' => $edificios
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los detalles del mobiliario.');
        }
    }
}
