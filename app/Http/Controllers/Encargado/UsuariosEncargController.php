<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\TipoUsuario;

class UsuariosEncargController extends Controller
{
    // Método para mostrar la vista de usuarios
    public function usuarios(Request $request)
    {
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
    }

    //metodo para registra usuario nuevos
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
            return redirect()->route('encargado.usuarios')->with('info', 'El usuario ya está registrado.');
            }

            // Crear el usuario
            Usuario::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'celular' => $request->celular,
                'TIPO_USUARIO_id_tipo_usu' => $request->tipo_usuario,
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->route('encargado.usuarios')->with('success', 'Usuario registrado con éxito.');
        } catch (\Exception $e) {
            // Redirigir con mensaje de información en caso de error
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo registrar el usuario. Por favor, inténtelo de nuevo.');
        }
    }

    // Método para registrar un tipo de usuario
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
                return redirect()->route('encargado.usuarios')->with('info', 'El tipo de usuario ya está registrado.');
            }

            // Crear el tipo de usuario
            TipoUsuario::create([
                'tipo' => $request->tipo,
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->route('encargado.usuarios')->with('success', 'Tipo de usuario registrado con éxito.');
        } catch (\Exception $e) {
            // Redirigir con mensaje de información en caso de error
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo registrar el tipo de usuario. Por favor, inténtelo de nuevo.');
        }
    }

    //editar usuario
    public function actualizarUsuario(Request $request, $id)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'celular' => 'required|string|max:20',
                'tipo_usuario' => 'required|exists:tipo_usuarios,id_tipo_usu',
            ]);

            $usuario = Usuario::findOrFail($id);

            // Normalizar los datos para la comparación
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
                return redirect()->route('encargado.usuarios')->with('info', 'El usuario ya está registrado.');
            }

            // Actualizar el usuario
            $usuario->update([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'celular' => $request->celular,
                'TIPO_USUARIO_id_tipo_usu' => $request->tipo_usuario,
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->route('encargado.usuarios')->with('success', 'Usuario actualizado con éxito.');
        } catch (\Exception $e) {
            // Redirigir con mensaje de información en caso de error
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo actualizar el usuario. Por favor, inténtelo de nuevo.');
        }
    }

    //eliminar usuario
    public function eliminarUsuario($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            // Redirigir con mensaje de éxito
            return redirect()->route('encargado.usuarios')->with('success', 'Usuario eliminado con éxito.');
        } catch (\Exception $e) {
            // Redirigir con mensaje de información en caso de error
            return redirect()->route('encargado.usuarios')->with('info', 'No se pudo eliminar el usuario. Por favor, inténtelo de nuevo.');
        }
    }

}
