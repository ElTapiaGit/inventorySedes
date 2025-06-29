<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Edificio;
use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\TipoPersonal;
use App\Models\Login;

class PersonalsController extends Controller
{
    // Mostrar la lista de personal
    public function index()
    {
        try{
            // Obtener todos los tipos de personal para el dropdown
            $tiposPersonal = TipoPersonal::all();

            // Obtener todos los registros de personal donde el estado es activo(1) y edificios
            //(se pone.sede para llamar al metod relacion del edificio model)
            $personalList = Personal::with('tipoPersonal', 'edificio.sede')
            ->where('estado', 1)//solo que estana activos
            ->get();

            //todo los edificios para los formularios
            $edificios = Edificio::all();

            return view('administrator.personal', [
            'personal' => $personalList,
            'tiposPersonal' => $tiposPersonal,
            'edificios' => $edificios
            ]);
            
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Ocurrio un Error al Obtener los personal para la pagina');
        }
    }

    // Guardar un nuevo personal
    public function store(Request $request)
    {
        try{
            $request->validate([
                'apellido_paterno' => 'required|string|max:15',
                'apellido_materno' => 'nullable|string|max:15',
                'nombre' => 'required|string|max:20',
                'numero_celular' => 'required|string|max:15',
                'tipo_personal' => 'required|exists:tipo_personal,id_tipo_per',
                'edificio' => 'required|exists:edificio,id_edificio'
            ]);
    
            // Verificar si el personal ya existe
            $personalExistente = Personal::where('nombre', $request->nombre)
                ->where('ap_paterno', $request->apellido_paterno)
                ->where('ap_materno', $request->apellido_materno)
                ->first();
    
            if ($personalExistente) {
                return redirect()->route('personal.index')->with('errorregister', 'El personal ya existe.');
            }
    
            $personal = new Personal();
            $personal->ap_paterno = $request->apellido_paterno;
            $personal->ap_materno = $request->apellido_materno;
            $personal->nombre = $request->nombre;
            $personal->celular = $request->numero_celular;
            $personal->estado = true; // Asumiendo que el nuevo personal está activo por defecto
            $personal->TIPO_PERSONAL_id_tipo_per = $request->tipo_personal;
            $personal->EDIFICIO_id_edificio = $request->edificio;
            $personal->save();
    
            return redirect()->route('personal.index')->with('successregister', 'Personal registrado exitosamente.');

        }catch (\Exception $e) {
            return redirect()->route('personal.index')->with('errordata', 'Error al agregar el nuevo personal: ');
        }
    }

    // Mostrar el formulario para editar un personal
    public function edit($id)
    {
        try{
            $personal = Personal::find($id);
            if (!$personal) {
                return redirect()->route('personal.index')->with('error', 'Personal no encontrado.');
            }
    
            $tiposPersonal = TipoPersonal::all();
    
            return view('personal_edit', [
                'personal' => $personal,
                'tiposPersonal' => $tiposPersonal
            ]);
        }catch (\Exception $e) {
            return redirect()->route('personal.index')->with('errordata', 'Error al agregar el encontra registro del personal: ');
        }
    }

    // Actualizar un personal existente
    public function update(Request $request, $id)
    {
        try{
            $request->validate([
                'nombre' => 'required|string|max:20',
                'apellido_paterno' => 'required|string|max:15',
                'apellido_materno' => 'nullable|string|max:15',
                'numero_celular' => 'required|string|max:15',
                'tipo_personal' => 'required|exists:tipo_personal,id_tipo_per',
                'edificio' => 'required|exists:edificio,id_edificio'
            ]);
    
            $personal = Personal::find($id);
            if (!$personal) {
                return redirect()->route('personal.index')->with('error', 'Personal no encontrado.');
            }
    
            $personal->ap_paterno = $request->apellido_paterno;
            $personal->ap_materno = $request->apellido_materno;
            $personal->nombre = $request->nombre;
            $personal->celular = $request->numero_celular;
            $personal->TIPO_PERSONAL_id_tipo_per = $request->tipo_personal;
            $personal->EDIFICIO_id_edificio = $request->edificio;
            $personal->save();
    
            return redirect()->route('personal.index')->with('success', 'Personal actualizado exitosamente.');
        }catch (\Exception $e) {

            return redirect()->route('personal.index')->with('errordata', 'Error al agregar el Actualizar personal: ');
        }
    }

    // Inhabilitar un personal
    public function destroy($id)
    {
        try{
            $personal = Personal::find($id);
            if (!$personal) {
                return redirect()->route('personal.index')->with('errordata', 'Personal no encontrado.');
            }
    
            // Cambiar el estado del personal a inactivo (0)
            $personal->estado = 0;
            $personal->save();
    
            return redirect()->route('personal.index')->with('success', 'Personal inactivo exitosamente.');

        }catch (\Exception $e) {
            return redirect()->route('personal.index')->with('errordata', 'Error al Inhabilitar al personal: ');
        }
    }

    //para guardar el login de acceso del personal
    public function asignarAcceso(Request $request)
    {
        try{
            $request->validate([
                'nombre_completo' => 'required|exists:personal,id_personal',
                'nombre_acceso' => 'required|string|max:50',
                'password' => 'required|string|min:6|confirmed',
            ]);
    
            $login = new Login();
            $login->nombre = $request->nombre_acceso;
            $login->contrasena = bcrypt($request->password);
            $login->PERSONAL_id_personal = $request->nombre_completo;
            $login->save();
    
            return redirect()->route('personal.index')->with('success', 'Acceso asignado correctamente');

        }catch (\Exception $e) {
            return redirect()->route('personal.index')->with('errordata', 'Error al dar Accesos al Personal: ');
        }
    }
}
