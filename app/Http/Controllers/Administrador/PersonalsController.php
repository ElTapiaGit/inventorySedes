<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Edificio;
use App\Models\Carrera;
use App\Models\Personal;
use App\Models\TipoPersonal;
use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PersonalsController extends Controller
{
    // Mostrar la lista de personal
    public function index()
    {
        try {
            $tiposPersonal = TipoPersonal::all();

            $personalList = Personal::with(['tipoPersonal', 'edificio.sede', 'edificiosAsignados', 'carrera', 'login'])
                ->where('estado', 1)
                ->get();

            $edificios = Edificio::orderBy('nombre_edi')->get();
            $carreras = Carrera::orderBy('nombre_carrera')->get();

            return view('administrator.personal', [
                'personal' => $personalList,
                'tiposPersonal' => $tiposPersonal,
                'edificios' => $edificios,
                'carreras' => $carreras,
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Ocurrio un Error al Obtener los personal para la pagina: ' . $e->getMessage());
        }
    }

    // Guardar un nuevo personal
    public function store(Request $request)
    {
        $tipoPersonal = TipoPersonal::findOrFail($request->input('tipo_personal'));
        $descRol = trim($tipoPersonal->descripcion_per ?? '');
        $isCoordinador = Str::startsWith(Str::lower($descRol), 'coordinador');
        $isEncargado = (Str::lower($descRol) === 'encargado');

        // Reglas de validacion segun el rol elegido fuera de try-catch
        $rules = [
            'nombre' => 'required|string|max:20',
            'apellido_paterno' => 'required|string|max:15',
            'apellido_materno' => 'nullable|string|max:15',
            'numero_celular' => 'required|string|max:15',
            'tipo_personal' => 'required|exists:tipo_personal,id_tipo_per',
        ];

        if ($isCoordinador) {
            $rules['CARRERA_id_carrera'] = 'required|integer|exists:carrera,id_carrera';
            $rules['edificio'] = 'required|integer|exists:edificio,id_edificio';
        } elseif ($isEncargado) {
            $rules['edificios'] = 'required|array|min:1';
            $rules['edificios.*'] = 'integer|exists:edificio,id_edificio';
        } else {
            $rules['edificio'] = 'required|integer|exists:edificio,id_edificio';
        }

        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 20 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El apellido paterno no debe superar los 15 caracteres.',
            'apellido_materno.max' => 'El apellido materno no debe superar los 15 caracteres.',
            'numero_celular.required' => 'El numero de celular es obligatorio.',
            'numero_celular.max' => 'El numero de celular no debe superar los 15 caracteres.',
            'tipo_personal.required' => 'Debe seleccionar un rol de personal.',
            'tipo_personal.exists' => 'El rol seleccionado no existe.',
            'CARRERA_id_carrera.required' => 'La carrera es obligatoria para el rol de Coordinador.',
            'CARRERA_id_carrera.exists' => 'La carrera seleccionada no existe.',
            'edificio.required' => 'Debe seleccionar un edificio.',
            'edificio.exists' => 'El edificio seleccionado no existe.',
            'edificios.required' => 'Debe asignar al menos un edificio al Encargado.',
            'edificios.min' => 'Debe seleccionar al menos un edificio asignado.',
            'edificios.*.exists' => 'Uno de los edificios seleccionados no existe.',
        ];

        $request->validate($rules, $messages);

        // Verificar si el personal ya existe
        $personalExistente = Personal::where('nombre', trim($request->nombre))
            ->where('ap_paterno', trim($request->apellido_paterno))
            ->where('ap_materno', $request->filled('apellido_materno') ? trim($request->apellido_materno) : null)
            ->first();

        if ($personalExistente) {
            return redirect()->route('personal.index')->with('errorregister', 'El personal ya existe en el sistema.');
        }

        try {
            $personal = new Personal();
            $personal->ap_paterno = trim($request->apellido_paterno);
            $personal->ap_materno = $request->filled('apellido_materno') ? trim($request->apellido_materno) : null;
            $personal->nombre = trim($request->nombre);
            $personal->celular = trim($request->numero_celular);
            $personal->estado = 1;
            $personal->TIPO_PERSONAL_id_tipo_per = $tipoPersonal->id_tipo_per;

            if ($isCoordinador) {
                $personal->CARRERA_id_carrera = (int) $request->CARRERA_id_carrera;
                $personal->EDIFICIO_id_edificio = (int) $request->edificio;
                $personal->save();

                $personal->edificiosAsignados()->sync([
                    (int) $request->edificio => ['es_principal' => 1]
                ]);
            } elseif ($isEncargado) {
                $edificiosIds = array_map('intval', $request->input('edificios', []));
                $edificioPrincipal = $edificiosIds[0];

                $personal->CARRERA_id_carrera = null;
                $personal->EDIFICIO_id_edificio = $edificioPrincipal;
                $personal->save();

                $syncData = [];
                foreach ($edificiosIds as $idx => $ediId) {
                    $syncData[$ediId] = ['es_principal' => ($idx === 0 ? 1 : 0)];
                }
                $personal->edificiosAsignados()->sync($syncData);
            } else {
                $personal->CARRERA_id_carrera = null;
                $personal->EDIFICIO_id_edificio = (int) $request->edificio;
                $personal->save();

                $personal->edificiosAsignados()->sync([
                    (int) $request->edificio => ['es_principal' => 1]
                ]);
            }

            return redirect()->route('personal.index')->with('successregister', 'Personal registrado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('personal.index')->with('errordata', 'Error al agregar el nuevo personal: ' . $e->getMessage());
        }
    }

    // Mostrar el formulario para editar un personal
    public function edit($id)
    {
        try {
            $personal = Personal::with(['tipoPersonal', 'edificio', 'edificiosAsignados', 'carrera'])->find($id);
            if (!$personal) {
                return redirect()->route('personal.index')->with('error', 'Personal no encontrado.');
            }

            $tiposPersonal = TipoPersonal::all();
            $edificios = Edificio::orderBy('nombre_edi')->get();
            $carreras = Carrera::orderBy('nombre_carrera')->get();

            return view('personal_edit', [
                'personal' => $personal,
                'tiposPersonal' => $tiposPersonal,
                'edificios' => $edificios,
                'carreras' => $carreras,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('personal.index')->with('errordata', 'Error al encontrar registro del personal.');
        }
    }

    // Actualizar un personal existente
    public function update(Request $request, $id)
    {
        $personal = Personal::findOrFail($id);
        $tipoPersonal = TipoPersonal::findOrFail($request->input('tipo_personal'));
        $descRol = trim($tipoPersonal->descripcion_per ?? '');
        $isCoordinador = Str::startsWith(Str::lower($descRol), 'coordinador');
        $isEncargado = (Str::lower($descRol) === 'encargado');

        // Reglas de validacion segun el rol elegido fuera de try-catch
        $rules = [
            'nombre' => 'required|string|max:20',
            'apellido_paterno' => 'required|string|max:15',
            'apellido_materno' => 'nullable|string|max:15',
            'numero_celular' => 'required|string|max:15',
            'tipo_personal' => 'required|exists:tipo_personal,id_tipo_per',
        ];

        if ($isCoordinador) {
            $rules['CARRERA_id_carrera'] = 'required|integer|exists:carrera,id_carrera';
            $rules['edificio'] = 'required|integer|exists:edificio,id_edificio';
        } elseif ($isEncargado) {
            $rules['edificios'] = 'required|array|min:1';
            $rules['edificios.*'] = 'integer|exists:edificio,id_edificio';
        } else {
            $rules['edificio'] = 'required|integer|exists:edificio,id_edificio';
        }

        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 20 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El apellido paterno no debe superar los 15 caracteres.',
            'apellido_materno.max' => 'El apellido materno no debe superar los 15 caracteres.',
            'numero_celular.required' => 'El numero de celular es obligatorio.',
            'numero_celular.max' => 'El numero de celular no debe superar los 15 caracteres.',
            'tipo_personal.required' => 'Debe seleccionar un rol de personal.',
            'tipo_personal.exists' => 'El rol seleccionado no existe.',
            'CARRERA_id_carrera.required' => 'La carrera es obligatoria para el rol de Coordinador.',
            'CARRERA_id_carrera.exists' => 'La carrera seleccionada no existe.',
            'edificio.required' => 'Debe seleccionar un edificio.',
            'edificio.exists' => 'El edificio seleccionado no existe.',
            'edificios.required' => 'Debe asignar al menos un edificio al Encargado.',
            'edificios.min' => 'Debe seleccionar al menos un edificio asignado.',
            'edificios.*.exists' => 'Uno de los edificios seleccionados no existe.',
        ];

        $request->validate($rules, $messages);

        try {
            $personal->ap_paterno = trim($request->apellido_paterno);
            $personal->ap_materno = $request->filled('apellido_materno') ? trim($request->apellido_materno) : null;
            $personal->nombre = trim($request->nombre);
            $personal->celular = trim($request->numero_celular);
            $personal->TIPO_PERSONAL_id_tipo_per = $tipoPersonal->id_tipo_per;

            if ($isCoordinador) {
                $personal->CARRERA_id_carrera = (int) $request->CARRERA_id_carrera;
                $personal->EDIFICIO_id_edificio = (int) $request->edificio;
                $personal->save();

                $personal->edificiosAsignados()->sync([
                    (int) $request->edificio => ['es_principal' => 1]
                ]);
            } elseif ($isEncargado) {
                $edificiosIds = array_map('intval', $request->input('edificios', []));
                $edificioPrincipal = $edificiosIds[0];

                $personal->CARRERA_id_carrera = null;
                $personal->EDIFICIO_id_edificio = $edificioPrincipal;
                $personal->save();

                $syncData = [];
                foreach ($edificiosIds as $idx => $ediId) {
                    $syncData[$ediId] = ['es_principal' => ($idx === 0 ? 1 : 0)];
                }
                $personal->edificiosAsignados()->sync($syncData);
            } else {
                $personal->CARRERA_id_carrera = null;
                $personal->EDIFICIO_id_edificio = (int) $request->edificio;
                $personal->save();

                $personal->edificiosAsignados()->sync([
                    (int) $request->edificio => ['es_principal' => 1]
                ]);
            }

            return redirect()->route('personal.index')->with('success', 'Personal actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('personal.index')->with('errordata', 'Error al actualizar personal: ' . $e->getMessage());
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
