<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class MaterialAmbienteController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function mostrarMateriales($token)
    {
        try {
            try {
                $id_ambiente = (int) Crypt::decryptString($token);
            } catch (DecryptException $e) {
                abort(404, 'Ambiente no encontrado');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente($id_ambiente);

            $ambiente = DB::table('ambiente')
                ->where('id_ambiente', $id_ambiente)
                ->first();

            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }

            $materiales = DB::table('material')
                ->where('AMBIENTE_id_ambiente', $id_ambiente)
                ->get();

            // Obtener los edificios permitidos
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.ambienteMaterial', [
                'ambiente' => $ambiente,
                'materiales' => $materiales,
                'edificios' => $edificios,
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los materiales del laboratorio.');
        }
    }
}
