<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuariosController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            // Obtener los valores de búsqueda desde la solicitud
            $tipo_usuario = $request->input('tipo_usuario');
            $nombre_usuario = $request->input('nombre_usuario');
            
            // Inicializar la consulta base
            $query = Usuario::query();
            
            // Filtro por tipo de usuario
            if ($tipo_usuario) {
                $query->whereHas('tipoUsuario', function($q) use ($tipo_usuario) {
                    $q->where('tipo', 'like', '%' . $tipo_usuario . '%');
                });
            }
            
            // Filtro por nombre o apellidos de usuario
            if ($nombre_usuario) {
                $query->where(function($q) use ($nombre_usuario) {
                    $q->where('nombre', 'like', '%' . $nombre_usuario . '%')
                      ->orWhere('apellidos', 'like', '%' . $nombre_usuario . '%');
                });
            }

            // Ejecutar la consulta con las relaciones necesarias
            $usuarios = $query->with('tipoUsuario')->get();

            // Si no hay resultados, redirigir con un mensaje de advertencia
            if ($usuarios->isEmpty()) {
                return redirect()->route('usuario.index')->with('warning', 'No se encontraron resultados.');
            }
            
            return view('administrator.usuario', ['usuarios' => $usuarios]);
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('Error', 'Hubo un problema al obtener los datos de los usuarios.');
        }
    }
}
