<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\TipoUsuario;

class UsuariosEncargController extends Controller
{
    // Metodo para mostrar la vista de usuarios
    public function usuarios(Request $request)
    {
        try {
            $tipoUsuarios = TipoUsuario::all();
            $query = Usuario::query();

            // Filtrar por nombre
            if ($request->has('nombre') && $request->nombre != '') {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            }

            // Filtrar por tipo de usuario
            if ($request->has('tipo_usuario') && $request->tipo_usuario != '') {
                $query->where('TIPO_USUARIO_id_tipo_usu', $request->tipo_usuario);
            }

            $usuarios = $query->get();

            return view('encargados.registrarUsuarios', compact('usuarios', 'tipoUsuarios'));
        } catch (\Exception $e) {
            return redirect()->route('encargado.inicio')->with('error', 'Error al obtener la lista de usuarios.');
        }
    }

    // Metodo para registrar usuarios nuevos
    public function registrarUsuario(Request $request)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'celular' => 'required|string|max:20',
                'tipo_usuario' => 'required|exists:tipo_usuario,id_tipo_usu',
            ]);

            // Verificar si el usuario ya existe
            $usuarioExistente = Usuario::where('nombre', $request->nombre)
                ->where('apellidos', $request->apellidos)
                ->where('celular', $request->celular)
                ->exists();

            if ($usuarioExistente) {
                return redirect()->route('encargado.usuarios')->with('info', 'El usuario ya esta registrado.');
            }

            // Crear el usuario
            Usuario::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'celular' => $request->celular,
                'TIPO_USUARIO_id_tipo_usu' => $request->tipo_usuario,
            ]);

            // Redirigir con mensaje de exito
            return redirect()->route('encargado.usuarios')->with('success', 'Usuario registrado con exito.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo registrar el usuario. Por favor, intentelo de nuevo.');
        }
    }

    // Metodo para registrar un tipo de usuario
    public function registrarTipoUsuario(Request $request)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'tipo' => 'required|string|max:255',
            ]);

            // Verificar si el tipo de usuario ya existe
            $tipoUsuarioExistente = TipoUsuario::where('tipo', $request->tipo)->exists();

            if ($tipoUsuarioExistente) {
                return redirect()->route('encargado.usuarios')->with('info', 'El tipo de usuario ya esta registrado.');
            }

            // Crear el tipo de usuario
            TipoUsuario::create([
                'tipo' => $request->tipo,
            ]);

            // Redirigir con mensaje de exito
            return redirect()->route('encargado.usuarios')->with('success', 'Tipo de usuario registrado con exito.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo registrar el tipo de usuario. Por favor, intentelo de nuevo.');
        }
    }

    // Editar usuario
    public function actualizarUsuario(Request $request, $id)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'celular' => 'required|string|max:20',
                'tipo_usuario' => 'required|exists:tipo_usuario,id_tipo_usu',
            ]);

            $usuario = Usuario::findOrFail($id);

            // Normalizar los datos para la comparacion
            $nombre = strtolower(trim($request->nombre));
            $apellidos = strtolower(trim($request->apellidos));
            $celular = trim($request->celular);

            // Verificar si el usuario ya existe (excepto el actual)
            $usuarioExistente = Usuario::whereRaw('LOWER(nombre) = ?', [$nombre])
                                ->whereRaw('LOWER(apellidos) = ?', [$apellidos])
                                ->where('celular', $celular)
                                ->where('id_usuario', '!=', $id)
                                ->exists();

            if ($usuarioExistente) {
                return redirect()->route('encargado.usuarios')->with('info', 'El usuario ya esta registrado.');
            }

            // Actualizar el usuario
            $usuario->update([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'celular' => $request->celular,
                'TIPO_USUARIO_id_tipo_usu' => $request->tipo_usuario,
            ]);

            // Redirigir con mensaje de exito
            return redirect()->route('encargado.usuarios')->with('success', 'Usuario actualizado con exito.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo actualizar el usuario. Por favor, intentelo de nuevo.');
        }
    }

    // Eliminar usuario
    public function eliminarUsuario($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            // Redirigir con mensaje de exito
            return redirect()->route('encargado.usuarios')->with('success', 'Usuario eliminado con exito.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo eliminar el usuario. Por favor, intentelo de nuevo.');
        }
    }
}
