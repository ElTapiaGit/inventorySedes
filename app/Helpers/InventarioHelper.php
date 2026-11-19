<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('inventario_img_url')) {
    function inventario_img_url(?string $ruta, string $default = 'img/placeholder-articulo.svg'): string
    {
        if ($ruta === null) {
            return asset($default);
        }

        $rutaLimpia = trim(ltrim($ruta, '/'));

        if ($rutaLimpia === '') {
            return asset($default);
        }

        $rutaMinuscula = strtolower($rutaLimpia);
        $cadenasInvalidas = [
            'foto eliminar',
            'sin_foto',
            'sin foto',
            'null',
            'n/a',
            'na',
            'none',
            'undefined',
        ];

        if (in_array($rutaMinuscula, $cadenasInvalidas, true)) {
            return asset($default);
        }

        if (Str::startsWith($rutaLimpia, 'http://') || Str::startsWith($rutaLimpia, 'https://')) {
            return $ruta;
        }

        if (Str::startsWith($rutaLimpia, 'inventario/')) {
            if (Storage::disk('public')->exists($rutaLimpia) || file_exists(public_path('storage/' . $rutaLimpia))) {
                return asset('storage/' . $rutaLimpia);
            }
            return asset($default);
        }

        if (Str::startsWith($rutaLimpia, 'storage/')) {
            $rutaRelativa = Str::after($rutaLimpia, 'storage/');
            if (Storage::disk('public')->exists($rutaRelativa) || file_exists(public_path($rutaLimpia))) {
                return asset($rutaLimpia);
            }
            return asset($default);
        }

        if (file_exists(public_path($rutaLimpia))) {
            return asset($rutaLimpia);
        }

        return asset($default);
    }
}

