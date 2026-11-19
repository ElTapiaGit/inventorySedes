<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AmbienteClinicaController extends Controller
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
                $idPiso = (int) Crypt::decrypt($id);
            } catch (DecryptException $e) {
                abort(404, 'Piso no encontrado');
            }

            $pisos = DB::table('piso')
                ->where('id_piso', $idPiso)
                ->first();

            if (!$pisos) {
                abort(404, 'Piso no encontrado');
            }

            // Blindaje IDOR: validar que el edificio del piso pertenezca al coordinador
            $this->scopeService->validarAccesoEdificio((int) $pisos->EDIFICIO_id_edificio);

            $edificios = $this->scopeService->getEdificiosPermitidos();
            $ambientesPermitidosIds = $this->scopeService->getAmbientesPermitidosIds((int) $pisos->EDIFICIO_id_edificio);

            // Obtener exclusivamente los ambientes autorizados del piso
            $ambientes = DB::table('tipo_ambiente')
                ->join('ambiente', 'tipo_ambiente.id_tipoambiente', '=', 'ambiente.TIPO_AMBIENTE_id_ambiente')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->where('piso.id_piso', $idPiso)
                ->whereIn('ambiente.id_ambiente', $ambientesPermitidosIds)
                ->select(
                    'ambiente.id_ambiente',
                    'tipo_ambiente.nombre_amb as tipo_ambiente',
                    'ambiente.nombre as nombre_ambiente',
                    'ambiente.descripcion_amb as descripcion_ambiente'
                )
                ->get();

            return view('coordinator.clinicas.ambienteClinica', [
                'ambientes' => $ambientes,
                'edificios' => $edificios,
                'pisos' => $pisos,
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina de ambientes.');
        }
    }
}
