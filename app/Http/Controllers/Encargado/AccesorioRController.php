<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Accesorio;

class AccesorioRController extends Controller
{
    // Metodo para mostrar el formulario de registro de accesorio
    public function create(){
        $fotos = Foto::all(); // Obtener todas las fotos para el select
        return view('encargados.registroAccesorio', compact('fotos'));
    }

    // Método para almacenar la foto del accesorio
    public function storeFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Subir la imagen al servidor
        if ($request->hasFile('foto')) {
            // Obtén el archivo
            $file = $request->file('foto');

            // Define un nombre único para la foto, para evitar conflictos de nombres
            $filename = $file->getClientOriginalName();

            // Verificar si la ruta ya existe en la base de datos
            $existingFoto = Foto::where('ruta_foto', 'img/accesorios-img/' . $filename)->first();
            if ($existingFoto) {
                // Si la foto ya existe, redirige con un mensaje de que ya está registrada
                return redirect()->back()->with([
                    'aviso' => 'La foto ya está registrada.',
                    'foto_id' => $existingFoto->id_foto,
                    'ruta_foto' => $existingFoto->ruta_foto,
                ]);
            }

            $filePath = $file->storeAs('img/accesorios-img', $filename, 'public'); // Subir a la carpeta 'public/img/img/accesorios-img'

            // Mover la imagen a la carpeta correcta
            $file->move(public_path('img/accesorios-img'), $filename);

            // Guardar la ruta de la imagen en la base de datos
            $foto = new Foto();
            $foto->ruta_foto = $filePath; // Ruta almacenada será 'img/accesorios-img/nombre_de_archivo.ext'
            $foto->save();

            return redirect()->route('accesorio.create')
            ->with('ruta_foto', $foto->ruta_foto)
            ->with('foto_id', $foto->id_foto);
        }

        return redirect()->back()->with('error', 'Error al subir la foto del Accesorio.');
    }

    // Método para almacenar el accesorio
    public function storeAccesorio(Request $request)
    {
        try{
            $request->validate([
                'cod_accesorio' => 'required|max:50',
                'nombre_acce' => 'required|max:80',
                'estado_acce' => 'required|max:45',
                'vida_util' => 'required|max:45',
                'ubicacion' => 'required|max:120',
                'FOTO_id_foto' => 'required|exists:foto,id_foto'
            ]);

            // Verificar si el código del equipo ya existe
            $existingEquipo = Accesorio::where('cod_accesorio', $request->cod_accesorio)->first();
            if ($existingEquipo) {
                // Si el equipo ya existe, redirigir con un mensaje de información
                return redirect()->route('accesorio.create')->with('info', 'El código del accesorio ya existe.');
            }
       
            $accesorio = new Accesorio();
            $accesorio->cod_accesorio = $request->cod_accesorio;
            $accesorio->nombre_acce = $request->nombre_acce;
            $accesorio->descripcion_acce = $request->descripcion_acce;
            $accesorio->observacion_ace = $request->observacion_ace;
            $accesorio->estado_acce = $request->estado_acce;
            $accesorio->vida_util = $request->vida_util;
            $accesorio->ubicacion = $request->ubicacion;
            $accesorio->fch_registro_acce = now();
            $accesorio->FOTO_id_foto = $request->FOTO_id_foto;
            $accesorio->save();
    
            return redirect()->route('accesorio.create')->with('success', 'Accesorio registrado exitosamente');
        }catch (\Exception $e) {
            return redirect()->route('accesorio.create')->with('errordata', 'Problemas al registrar el Accesorio: FAVOR REVISAR LOS CAMPOS AL LLENAR');
        }
    }
}
