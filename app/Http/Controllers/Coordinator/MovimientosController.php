<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;

class MovimientosController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    protected function getMovimientosQuery(array $ambientesIds)
    {
        return DB::table('uso_ambiente')
            ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
            ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente')
            ->whereIn('uso_ambiente.AMBIENTE_id_ambiente', $ambientesIds)
            ->select(
                'uso_ambiente.id_uso_ambiente',
                'ambiente.nombre AS nombre_ambiente',
                DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                'uso_ambiente.descripcion',
                'uso_ambiente.semestre',
                'uso_ambiente.fch_uso',
                'uso_ambiente.hora_uso',
                'final_uso.fch_fin',
                DB::raw('IF(final_uso.id_uso_fin IS NOT NULL, 1, 0) AS uso_finalizado')
            );
    }

    public function index()
    {
        try {
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

            $nombresUsuarios = DB::table('usuario')
                ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
                ->get();

            $fechas = DB::table('uso_ambiente')
                ->whereIn('AMBIENTE_id_ambiente', $ambientesIds)
                ->select('fch_uso')
                ->distinct()
                ->get();

            $movimientos = $this->getMovimientosQuery($ambientesIds)->paginate(10);
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina movimientos.');
        }
    }

    public function buscar(Request $request)
    {
        try {
            $usuario = $request->input('usuario');
            $fecha = $request->input('fecha');
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

            $query = $this->getMovimientosQuery($ambientesIds);

            if (!empty($usuario)) {
                $query->where(DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos)"), 'like', '%' . $usuario . '%');
            }

            if (!empty($fecha)) {
                try {
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
                    $query->whereDate('uso_ambiente.fch_uso', $formattedDate);
                } catch (\Exception $e) {
                    session()->flash('error_fecha', 'Formato de fecha incorrecto. Use dd/mm/yyyy.');
                }
            }

            $movimientos = $query->paginate(10);

            if ($movimientos->isEmpty()) {
                if (!empty($usuario)) {
                    session()->flash('error_usuario', 'El nombre del usuario no esta registrado para este edificio.');
                }
                if (!empty($fecha)) {
                    session()->flash('error_fecha', 'La fecha ingresada no esta registrada.');
                }
            }

            $nombresUsuarios = DB::table('usuario')
                ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
                ->get();

            $fechas = DB::table('uso_ambiente')
                ->whereIn('AMBIENTE_id_ambiente', $ambientesIds)
                ->select('fch_uso')
                ->distinct()
                ->get();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Problema al procesar la busqueda de movimientos.');
        }
    }

    public function detalles($id)
    {
        try {
            $idDesencriptado = decrypt($id);

            $movimiento = DB::table('uso_ambiente')
                ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente')
                ->select(
                    'uso_ambiente.*',
                    'ambiente.nombre AS nombre_ambiente',
                    DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                    'final_uso.fch_fin',
                    'final_uso.hora_fin',
                    'final_uso.PERSONAL_id_personal AS personal_id_fin'
                )
                ->where('uso_ambiente.id_uso_ambiente', $idDesencriptado)
                ->first();

            if (!$movimiento) {
                return redirect()->route('movimientos.index')->withErrors(['error' => 'Movimiento no encontrado']);
            }

            // Blindaje IDOR: verificar pertenencia del ambiente al coordinador
            $this->scopeService->validarAccesoAmbiente((int) $movimiento->AMBIENTE_id_ambiente);

            $personal_inicio = DB::table('personal')
                ->select('nombre', 'ap_paterno', 'ap_materno', 'celular')
                ->where('id_personal', $movimiento->PERSONAL_id_personal)
                ->first();

            $personal_fin = null;
            if ($movimiento->fch_fin) {
                $personal_fin = DB::table('personal')
                    ->select('nombre', 'ap_paterno', 'ap_materno', 'celular')
                    ->where('id_personal', $movimiento->personal_id_fin)
                    ->first();
            }

            $edificios = $this->scopeService->getEdificiosPermitidos();
            return view('coordinator.detallesMovimiento', compact('movimiento', 'personal_inicio', 'personal_fin', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para detalle de movimiento.');
        }
    }

    // Metodos para contexto Clinica
    public function indexx()
    {
        try {
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

            $nombresUsuarios = DB::table('usuario')
                ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
                ->get();

            $fechas = DB::table('uso_ambiente')
                ->whereIn('AMBIENTE_id_ambiente', $ambientesIds)
                ->select('fch_uso')
                ->distinct()
                ->get();

            $movimientos = $this->getMovimientosQuery($ambientesIds)->paginate(10);
            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina movimientos.');
        }
    }

    public function buscarUserData(Request $request)
    {
        try {
            $usuario = $request->input('usuario');
            $fecha = $request->input('fecha');

            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

            $query = $this->getMovimientosQuery($ambientesIds);

            if (!empty($usuario)) {
                $query->where(DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos)"), 'like', '%' . $usuario . '%');
            }

            if (!empty($fecha)) {
                try {
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
                    $query->whereDate('uso_ambiente.fch_uso', $formattedDate);
                } catch (\Exception $e) {
                    session()->flash('error_fecha', 'Formato de fecha incorrecto. Use dd/mm/yyyy.');
                }
            }

            $movimientos = $query->paginate(10);

            $nombresUsuarios = DB::table('usuario')
                ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
                ->get();

            $fechas = DB::table('uso_ambiente')
                ->whereIn('AMBIENTE_id_ambiente', $ambientesIds)
                ->select('fch_uso')
                ->distinct()
                ->get();

            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos de busqueda.');
        }
    }

    public function detallesClinica($id)
    {
        return $this->detalles($id);
    }
}
