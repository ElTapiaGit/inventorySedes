<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class DetallesMaterialController extends Controller
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
                $materialId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                abort(404, 'Material no encontrado');
            }

            $material = DB::table('material')
                ->leftJoin('foto', 'material.FOTO_id_foto', '=', 'foto.id_foto')
                ->join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('material.cod_mate', $materialId)
                ->select(
                    'foto.ruta_foto',
                    'material.cod_mate',
                    'material.AMBIENTE_id_ambiente',
                    'ambiente.nombre',
                    'material.tipo_mate',
                    'material.descripcion_mate',
                    'material.estado_mate',
                    'material.observacion_mate',
                    'material.fch_registrada'
                )
                ->first();

            if (!$material) {
                abort(404, 'Material no encontrado');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente((int) $material->AMBIENTE_id_ambiente);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesMaterial', [
               'material' => $material,
               'edificios' => $edificios
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del material.');
        }
    }

    public function detalleMaterial($token)
    {
        try {
            try {
                $materialId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                abort(404, 'Material no encontrado');
            }

            $material = DB::table('material')
                ->leftJoin('foto', 'material.FOTO_id_foto', '=', 'foto.id_foto')
                ->join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('material.cod_mate', $materialId)
                ->select(
                    'foto.ruta_foto',
                    'material.cod_mate',
                    'material.AMBIENTE_id_ambiente',
                    'ambiente.nombre',
                    'material.tipo_mate',
                    'material.descripcion_mate',
                    'material.estado_mate',
                    'material.observacion_mate',
                    'material.fch_registrada'
                )
                ->first();

            if (!$material) {
                abort(404, 'Material no encontrado');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente((int) $material->AMBIENTE_id_ambiente);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesMaterial', [
                'material' => $material,
                'edificios' => $edificios
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los detalles del material.');
        }
    }
}
