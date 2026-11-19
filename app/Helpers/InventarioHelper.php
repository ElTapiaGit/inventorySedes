<?php

use Illuminate\Support\Str;

if (!function_exists('inventario_img_url')) {
    function inventario_img_url(?string $ruta, string $default = 'img/placeholder-articulo.svg'): string
    {
        if (empty($ruta)) {
            return asset($default);
        }

        $rutaLimpia = ltrim($ruta, '/');

        if (Str::startsWith($rutaLimpia, 'http://') || Str::startsWith($rutaLimpia, 'https://')) {
            return $ruta;
        }

        if (Str::startsWith($rutaLimpia, 'inventario/')) {
            return asset('storage/' . $rutaLimpia);
        }

        if (Str::startsWith($rutaLimpia, 'storage/')) {
            return asset($rutaLimpia);
        }

        return asset($rutaLimpia);
    }
}
