<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Edificio;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ContenidoAmbienteController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function show($id_ambiente)
    {
        try {
            try {
                $id = (int) Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no valido.');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente($id);

            // Obtener los detalles del ambiente
            $ambiente = DB::table('ambiente')
                ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                ->where('ambiente.id_ambiente', $id)
                ->select(DB::raw("CONCAT(tipo_ambiente.nombre_amb, ' - ', ambiente.nombre) AS nombre_completo_ambiente"), 'ambiente.id_ambiente')
                ->first();

            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }

            // Obtener los equipos del ambiente
            $equipos = DB::table('equipo')
                ->where('AMBIENTE_id_ambiente', $id)
                ->select('cod_equipo', 'nombre_equi', 'estado_equi', 'observaciones_equi', 'vida_util', 'fch_registro')
                ->get();

            // Obtener los materiales del ambiente
            $materiales = DB::table('material')
                ->where('AMBIENTE_id_ambiente', $id)
                ->select('cod_mate', 'tipo_mate', 'estado_mate', 'observacion_mate', 'fch_registrada')
                ->get();

            // Obtener los mobiliarios del ambiente
            $mobiliarios = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->where('AMBIENTE_id_ambiente', $id)
                ->select('mobiliario.cod_mueble', 'tipo_mobiliario.tipo_mueble', 'mobiliario.estado_mueb', 'mobiliario.observacion', 'mobiliario.vida_util', 'mobiliario.fch_registro')
                ->get();

            // Obtener los edificios permitidos
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.ambiente', compact('ambiente', 'equipos', 'materiales', 'mobiliarios', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos para la pagina ambientes.');
        }
    }

    public function showEquipos($id_ambiente)
    {
        try {
            try {
                $id = (int) Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no valido.');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente($id);

            $ambiente = DB::table('ambiente')->where('id_ambiente', $id)->first();
            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }

            $equipos = DB::table('equipo')->where('AMBIENTE_id_ambiente', $id)->get();
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.ambienteEquipo', compact('ambiente', 'equipos', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los equipos del ambiente.');
        }
    }

    public function showMateriales($id_ambiente)
    {
        try {
            try {
                $id = (int) Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no valido.');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente($id);

            $ambiente = DB::table('ambiente')->where('id_ambiente', $id)->first();
            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }

            $materiales = DB::table('material')->where('AMBIENTE_id_ambiente', $id)->get();
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.ambienteMaterial', compact('ambiente', 'materiales', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los materiales del ambiente.');
        }
    }

    public function showMobiliarios($id_ambiente)
    {
        try {
            try {
                $id = (int) Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no valido.');
            }

            // Blindaje IDOR
            $this->scopeService->validarAccesoAmbiente($id);

            $ambiente = DB::table('ambiente')->where('id_ambiente', $id)->first();
            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }

            $mobiliarios = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->where('AMBIENTE_id_ambiente', $id)
                ->get();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.ambienteMobiliario', compact('ambiente', 'mobiliarios', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener el mobiliario del ambiente.');
        }
    }

    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $pisoSeleccionado = $request->input('piso');

            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioActivoId = $this->scopeService->getEdificioActivoId();

            // Identificar el edificio clinico si existe entre los permitidos, o el activo
            $edificioClinica = $edificiosPermitidos->first(function ($item) {
                return str_contains(mb_strtolower($item->nombre_edi), 'clinica');
            }) ?? $edificiosPermitidos->firstWhere('id_edificio', $edificioActivoId) ?? $edificiosPermitidos->first();

            $targetEdificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $edificioActivoId;

            // Obtener los pisos del edificio clinico/activo
            $pisos = DB::table('piso')
                ->where('EDIFICIO_id_edificio', $targetEdificioId)
                ->select('piso.id_piso', 'piso.numero_piso')
                ->orderBy('piso.numero_piso')
                ->get();

            $ambientesPermitidosIds = $this->scopeService->getAmbientesPermitidosIds($targetEdificioId);

            // Obtener los ambientes del edificio con filtros
            $query = DB::table('piso')
                ->join('ambiente', 'piso.id_piso', '=', 'ambiente.PISO_id_piso')
                ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                ->where('piso.EDIFICIO_id_edificio', $targetEdificioId)
                ->whereIn('ambiente.id_ambiente', $ambientesPermitidosIds)
                ->select(
                    'piso.numero_piso',
                    'ambiente.id_ambiente',
                    'tipo_ambiente.nombre_amb as tipo_ambiente',
                    'ambiente.nombre as nombre_ambiente',
                    'ambiente.descripcion_amb as descripcion_ambiente'
                );

            if ($search) {
                $query->where('ambiente.nombre', 'LIKE', "%$search%");
            }

            if ($pisoSeleccionado) {
                $query->where('piso.numero_piso', $pisoSeleccionado);
            }

            $ambientes = $query->get();

            $edificios = $edificiosPermitidos;

            return view('coordinator.ambientes', compact('pisos', 'ambientes', 'edificios', 'pisoSeleccionado'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos para la pagina ambientes.');
        }
    }
}
