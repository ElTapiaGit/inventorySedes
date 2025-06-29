<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Personal;
use App\Models\Ambiente;
use App\Models\Material;
use Carbon\Carbon;

class MaterialController extends Controller
{
    // Mostrar la vista del formulario de registro de material
    public function create()
    {
        // Obtén el ID del usuario actualmente autenticado
        $userId = auth()->user()->id_personal; // Asegúrate de que esto corresponda con tu implementación de autenticación

        // Obtener el id del edificio donde el personal trabaja
        $edificioId = Personal::where('id_personal', $userId)->value('EDIFICIO_id_edificio');

        // Obtén los ambientes asociados a ese edificio
        $ambientes = Ambiente::whereHas('piso', function ($query) use ($edificioId) {
            $query->where('Edificio_id_edificio', $edificioId);
        })->get();
        $fotos = Foto::all();

        return view('encargados.registrarMaterial', compact('ambientes', 'fotos'));
    }

    // Guardar la foto del material
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
            $existingFoto = Foto::where('ruta_foto', 'img/materiales-img/' . $filename)->first();
            if ($existingFoto) {
                // Si la foto ya existe, redirige con un mensaje de que ya está registrada
                return redirect()->back()->with([
                    'aviso' => 'La foto ya está registrada.',
                    'foto_id' => $existingFoto->id_foto,
                    'ruta_foto' => $existingFoto->ruta_foto,
                ]);
            }

            $filePath = $file->storeAs('img/materiales-img', $filename, 'public'); // Subir a la carpeta 'public/img/img/materiales-img'

            // Mover la imagen a la carpeta correcta
            $file->move(public_path('img/materiales-img'), $filename);

            // Guardar la ruta de la imagen en la base de datos
            $foto = new Foto();
            $foto->ruta_foto = $filePath; // Ruta almacenada será 'img/materiales-img/nombre_de_archivo.ext'
            $foto->save();

            return redirect()->route('material.create')
            ->with('ruta_foto', $foto->ruta_foto)
            ->with('foto_id', $foto->id_foto);

        }
        return redirect()->back()->with('error', 'Error al subir la foto.');
    }

    // Guardar el material en la base de datos
    public function storeMaterial(Request $request)
    {
        $request->validate([
            'cod_mate' => 'required|string|max:50',
            'tipo_mate' => 'required|string|max:50',
            'descripcion_mate' => 'required|string',
            'estado_mate' => 'required|string|max:45',
            'observacion_mate' => 'nullable|string|max:80',
            'AMBIENTE_id_ambiente' => 'required|integer|exists:ambiente,id_ambiente',
            'FOTO_id_foto' => 'required|integer|exists:foto,id_foto',
        ]);

        // Guardar el material en la base de datos
        $material = new Material();
        $material->cod_mate = $request->cod_mate;
        $material->tipo_mate = $request->tipo_mate;
        $material->descripcion_mate = $request->descripcion_mate;
        $material->estado_mate = $request->estado_mate;
        $material->observacion_mate = $request->observacion_mate;
        $material->AMBIENTE_id_ambiente = $request->AMBIENTE_id_ambiente;
        $material->FOTO_id_foto = $request->FOTO_id_foto;
        $material->fch_registrada = now();
        $material->save();

        return redirect()->route('material.create')->with('success', 'Material registrado exitosamente');
    }
}
