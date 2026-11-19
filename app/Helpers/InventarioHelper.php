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

if (!function_exists('inventario_estado_badge')) {
    function inventario_estado_badge(?string $estado): string
    {
        if ($estado === null) {
            return 'bg-secondary';
        }

        $estadoLimpio = mb_strtolower(trim($estado));

        if ($estadoLimpio === '') {
            return 'bg-secondary';
        }

        $exitosos = ['nuevo', 'bueno', 'operativo', 'funcionando'];
        if (in_array($estadoLimpio, $exitosos, true)) {
            return 'bg-success';
        }

        if ($estadoLimpio === 'regular') {
            return 'bg-warning text-dark';
        }

        $mantenimiento = ['para mantenimiento', 'en mantenimiento', 'para reparar', 'para reponer', 'para cambiar'];
        foreach ($mantenimiento as $palabra) {
            if (str_contains($estadoLimpio, $palabra)) {
                return 'bg-info text-dark';
            }
        }

        $criticos = ['para descarte', 'malo', 'danado', 'dañado'];
        foreach ($criticos as $critico) {
            if (str_contains($estadoLimpio, $critico)) {
                return 'bg-danger';
            }
        }

        return 'bg-secondary';
    }
}


