<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
/* Carbon es una biblioteca de PHP que facilita el trabajo con fechas y horas. 
*  Está construida sobre la clase DateTime de PHP y proporciona una API más simple e 
*  intuitiva para realizar operaciones comunes con fechas y horas, como sumar o restar días, 
*  formatear fechas, comparar fechas, y muchas otras funcionalidades.*/
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
////////// CONTROLADOR PARA LAS PAGINAS MOVIMIENTOS EN LOS EDIFICIOS CENTRALES Y CLINICAS
class MovimientosController extends Controller
{
    //Para las paginas del edificio central
    public function index()
    {
       try{
            // Obtener los nombres de los usuarios para el datalist
            $nombresUsuarios = DB::table('usuario')
            ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
            ->get();

            // Obtener las fechas de uso
            $fechas = DB::table('uso_ambiente')
                ->select('fch_uso')
                ->distinct()
                ->get();

            // Obtener todos los movimientos iniciales
            $movimientos = DB::table('uso_ambiente')
                ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente')
                ->where('edificio.nombre_edi', 'Edificio Central')//poder agregar un AND para filtrar la sedes y ambiente laboratorio de odontologia
                ->where('tipo_ambiente.nombre_amb', 'Laboratorio Odontologia') //y que sean de los ambientes de laboratorio odontologia
                ->select(
                    'uso_ambiente.id_uso_ambiente',
                    'ambiente.nombre AS nombre_ambiente',
                    DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                    'uso_ambiente.descripcion',
                    'uso_ambiente.semestre',
                    'uso_ambiente.fch_uso',
                    'uso_ambiente.hora_uso',
                    'final_uso.fch_fin',
                    DB::raw('IF(final_uso.id_uso_fin IS NOT NULL, 1, 0) AS uso_finalizado') // Indicador de uso finalizado
                )
                ->paginate(10); // Paginación de 10 registros por página

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                        ->where('sede.nombre', 'Sede Central')
                        ->select('edificio.nombre_edi')
                        ->get();

            return view('coordinator.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
       }catch(\Exception $e) {
        return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina: ');
    }
    }

    public function buscar(Request $request)
    {
        try{
            $usuario = $request->input('usuario');
            $fecha = $request->input('fecha');

            // Consulta para buscar por nombre de usuario o por fecha
            $query = DB::table('uso_ambiente')
                ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente') // Left join para verificar si el uso ha finalizado
                ->where('edificio.nombre_edi', 'Edificio Central')
                ->where('tipo_ambiente.nombre_amb', 'Laboratorio Odontologia')
                ->select(
                    'uso_ambiente.id_uso_ambiente',
                    'ambiente.nombre AS nombre_ambiente',
                    DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                    'uso_ambiente.descripcion',
                    'uso_ambiente.semestre',
                    'uso_ambiente.fch_uso',
                    'uso_ambiente.hora_uso',
                    DB::raw('IF(final_uso.id_uso_fin IS NOT NULL, 1, 0) AS uso_finalizado') // Indicador de uso finalizado
                );

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
                    session()->flash('error_usuario', 'El nombre del usuario no está registrado para este edificio.');
                }
                if (!empty($fecha)) {
                    session()->flash('error_fecha', 'La fecha ingresada no está registrada.');
                }
            }

            // Obtener los nombres de los usuarios para el datalist
            $nombresUsuarios = DB::table('usuario')
                ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
                ->get();

            // Obtener las fechas de uso
            $fechas = DB::table('uso_ambiente')
                ->select('fch_uso')
                ->distinct()
                ->get();

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Problema al obtener los datos de busqueda revisar los campos: ');
        }
    }

    // Método para mostrar los detalles del movimiento
    public function detalles($id)
    {
        try{
            $idDesencriptado = decrypt($id);

            // Consulta para obtener los detalles del movimiento
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
                    'final_uso.PERSONAL_id_personal AS personal_id_fin' // Agregamos este campo para poder usarlo más adelante
                )
                ->where('uso_ambiente.id_uso_ambiente', $idDesencriptado)
                ->first();

            if (!$movimiento) {
                return redirect()->route('movimientos.index')->withErrors(['error' => 'Movimiento no encontrado']);
            }

            // Obtener los datos del personal que inició el uso
            $personal_inicio = DB::table('personal')
                ->select('nombre', 'ap_paterno', 'ap_materno', 'celular')
                ->where('id_personal', $movimiento->PERSONAL_id_personal)
                ->first();

            // Obtener los datos del personal que finalizó el uso (si existe)
            $personal_fin = null;
            if ($movimiento->fch_fin) {
                $personal_fin = DB::table('personal')
                    ->select('nombre', 'ap_paterno', 'ap_materno', 'celular')
                    ->where('id_personal',  $movimiento->personal_id_fin) // Utiliza el ID del personal que finalizó el uso
                    ->first();
            }

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                        ->where('sede.nombre', 'Sede Central')
                        ->select('edificio.nombre_edi')
                        ->get();
            return view('coordinator.detallesMovimiento', compact('movimiento', 'personal_inicio', 'personal_fin', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para detalle de movimiento: ');
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //Para las paginas de la clinica odontologica
    public function indexx()
    {
        try{
            // Obtener los nombres de los usuarios para el datalist
            $nombresUsuarios = DB::table('usuario')
            ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
            ->get();

            // Obtener las fechas de uso
            $fechas = DB::table('uso_ambiente')
                ->select('fch_uso')
                ->distinct()
                ->get();

            // Obtener todos los movimientos iniciales
            $movimientos = DB::table('uso_ambiente')
                ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente') // Left join para verificar si el uso ha finalizado
                ->where('edificio.nombre_edi', 'Clinica Odontologia')//poder agregar un AND para filtrar la sedes
                ->select(
                    'uso_ambiente.id_uso_ambiente',
                    'ambiente.nombre AS nombre_ambiente',
                    DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                    'uso_ambiente.descripcion',
                    'uso_ambiente.semestre',
                    'uso_ambiente.fch_uso',
                    'uso_ambiente.hora_uso',
                    DB::raw('IF(final_uso.id_uso_fin IS NOT NULL, 1, 0) AS uso_finalizado') // Indicador de uso finalizado
                )
                ->paginate(10); // Paginación de 10 registros por página

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                        ->where('sede.nombre', 'Sede Central')
                        ->select('edificio.nombre_edi')
                        ->get();

            return view('coordinator.clinicas.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina movimientos: ' . $e->getMessage());
        }
    }

    public function buscarUserData(Request $request)
    {
        try{
            $usuario = $request->input('usuario');
            $fecha = $request->input('fecha');

            // Consulta para buscar por nombre de usuario o por fecha
            $query = DB::table('uso_ambiente')
                ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente') // Left join para verificar si el uso ha finalizado
                ->where('edificio.nombre_edi', 'Clinica Odontologia')
                ->select(
                    'uso_ambiente.id_uso_ambiente',
                    'ambiente.nombre AS nombre_ambiente',
                    DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                    'uso_ambiente.descripcion',
                    'uso_ambiente.semestre',
                    'uso_ambiente.fch_uso',
                    'uso_ambiente.hora_uso',
                    DB::raw('IF(final_uso.id_uso_fin IS NOT NULL, 1, 0) AS uso_finalizado') // Indicador de uso finalizado
                );



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

            // Obtener los nombres de los usuarios para el datalist
            $nombresUsuarios = DB::table('usuario')
                ->select(DB::raw("CONCAT(nombre, ' ', apellidos) AS nombre"))
                ->get();

            // Obtener las fechas de uso
            $fechas = DB::table('uso_ambiente')
                ->select('fch_uso')
                ->distinct()
                ->get();

                // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('sede.nombre', 'Sede Central')
            ->select('edificio.nombre_edi')
            ->get();

            return view('coordinator.clinicas.movimientos', compact('nombresUsuarios', 'fechas', 'movimientos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos de busqueda revisar los campos: ' . $e->getMessage());
        }
    }

    // Método para mostrar los detalles del movimiento de la clinica
    public function detallesClinica($id)
    {
        try {
            $idDesencriptado = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        // Consulta para obtener los detalles del movimiento
        $movimiento = DB::table('uso_ambiente')
            ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
            ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente')
            ->select(
                'uso_ambiente.*',
                'ambiente.nombre AS nombre_ambiente',
                DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                'final_uso.fch_fin',
                'final_uso.hora_fin'
            )
            ->where('uso_ambiente.id_uso_ambiente', $idDesencriptado)
            ->first();

        if (!$movimiento) {
            return redirect()->route('clinica.movimientos.index')->withErrors(['error' => 'Movimiento no encontrado']);
        }

        // Obtener los datos del personal que inició el uso
        $personal_inicio = DB::table('personal')
            ->select('nombre', 'ap_paterno', 'ap_materno', 'celular')
            ->where('id_personal', $movimiento->PERSONAL_id_personal)
            ->first();

        // Obtener los datos del personal que finalizó el uso (si existe)
        $personal_fin = null;
        if ($movimiento->fch_fin) {
            $personal_fin = DB::table('personal')
                ->select('nombre', 'ap_paterno', 'ap_materno', 'celular')
                ->where('id_personal', $movimiento->PERSONAL_id_personal)
                ->first();
        }

        // Obtener los edificios para el menú desplegable
        $edificios = DB::table('edificio')
                      ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                      ->where('sede.nombre', 'Sede Central')
                      ->select('edificio.nombre_edi')
                      ->get();
        return view('coordinator.clinicas.detallesMovimientos', compact('movimiento', 'personal_inicio', 'personal_fin', 'edificios'));
    }
}
