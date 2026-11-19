<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class DetallesLaboratorioController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function show($id)
    {
        try {
            try {
                $idDesencriptado = (int) Crypt::decrypt($id);
            } catch (DecryptException $e) {
                abort(404, 'Ambiente no encontrado');
            }

            // Blindaje IDOR: validar pertenencia territorial/academica
            $this->scopeService->validarAccesoAmbiente($idDesencriptado);

            // Obtener los detalles del ambiente
            $ambiente = DB::table('ambiente')->where('id_ambiente', $idDesencriptado)->first();
            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }

            // Obtener los equipos del ambiente
            $equipos = DB::table('equipo')
                ->where('AMBIENTE_id_ambiente', $idDesencriptado)
                ->select('cod_equipo', 'nombre_equi', 'estado_equi', 'observaciones_equi', 'vida_util', 'fch_registro')
                ->get();

            // Obtener los materiales del ambiente
            $materiales = DB::table('material')
                ->where('AMBIENTE_id_ambiente', $idDesencriptado)
                ->select('cod_mate', 'tipo_mate', 'estado_mate', 'observacion_mate', 'fch_registrada')
                ->get();

            // Obtener los mobiliarios del ambiente
            $mobiliarios = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->where('AMBIENTE_id_ambiente', $idDesencriptado)
                ->select('mobiliario.cod_mueble', 'tipo_mobiliario.tipo_mueble', 'mobiliario.estado_mueb', 'mobiliario.observacion', 'mobiliario.vida_util', 'mobiliario.fch_registro')
                ->get();

            // Obtener los edificios permitidos
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.ambiente', compact('ambiente', 'equipos', 'materiales', 'mobiliarios', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del laboratorio.');
        }
    }
}
