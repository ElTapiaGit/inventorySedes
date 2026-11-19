<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class MobiliarioAmbienteController extends Controller
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
                $id_ambiente = (int) Crypt::decryptString($token);
            } catch (DecryptException $e) {
                abort(404, 'Ambiente no encontrado');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente($id_ambiente);

            // Obtener informacion del ambiente
            $ambiente = DB::table('ambiente')
                ->where('id_ambiente', $id_ambiente)
                ->first();

            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }

            // Obtener mobiliario del ambiente
            $mobiliarios = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->where('AMBIENTE_id_ambiente', $id_ambiente)
                ->select('mobiliario.cod_mueble', 'tipo_mobiliario.tipo_mueble', 'mobiliario.estado_mueb', 'mobiliario.observacion', 'mobiliario.vida_util', 'mobiliario.fch_registro')
                ->get();

            // Obtener los edificios permitidos
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.ambienteMobiliario', [
                'ambiente' => $ambiente,
                'mobiliarios' => $mobiliarios,
                'edificios' => $edificios
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los mobiliarios del laboratorio.');
        }
    }
}
