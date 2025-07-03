<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use Illuminate\Support\Facades\Crypt;

class DetallePrestamoController extends Controller
{
    public function show($id)
    {
        try {
            $idDesencriptado = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID de préstamo inválido');
        }

        $prestamo = Prestamo::with(['detallePrestamos.ambiente', 'personal', 'devolucion'])->findOrFail($idDesencriptado);

        return view('encargados.detallePrestamos', compact('prestamo'));
    }
}
