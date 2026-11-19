<?php

namespace App\Services;

use App\Models\Foto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class InventarioImageService
{
    /**
     * Categorias de inventario permitidas para almacenamiento organizado.
     */
    public const ALLOWED_CATEGORIES = [
        'equipos',
        'mobiliarios',
        'materiales',
        'accesorios'
    ];

    /**
     * Ancho maximo en pixeles para redimensionado proporcional.
     */
    public const MAX_WIDTH = 1280;

    /**
     * Calidad de compresion WebP (0-100).
     */
    public const WEBP_QUALITY = 80;

    /**
     * Valida que la categoria solicitada este dentro de la lista blanca.
     *
     * @param string $categoria
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateCategory(string $categoria): void
    {
        if (!in_array($categoria, self::ALLOWED_CATEGORIES, true)) {
            throw new InvalidArgumentException(
                "Categoria '{$categoria}' no permitida. Categorias validas: " . implode(', ', self::ALLOWED_CATEGORIES)
            );
        }
    }

    /**
     * Procesa, redimensiona y convierte un archivo subido a formato .webp con nombre UUID.
     * Almacena el resultado en storage/app/public/inventario/{categoria}/.
     *
     * @param UploadedFile $file
     * @param string $categoria
     * @return string Ruta relativa limpia (ej. inventario/equipos/uuid.webp)
     * @throws RuntimeException|InvalidArgumentException
     */
    public function storeWebpFile(UploadedFile $file, string $categoria): string
    {
        $this->validateCategory($categoria);

        if (!extension_loaded('gd') || !function_exists('imagewebp')) {
            throw new RuntimeException(
                "La extension PHP GD con soporte WebP no se encuentra habilitada en el servidor."
            );
        }

        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            throw new RuntimeException("No se pudo acceder al archivo temporal subido.");
        }

        $rawContent = file_get_contents($realPath);
        if ($rawContent === false) {
            throw new RuntimeException("Error al leer los datos de la imagen subida.");
        }

        $srcImage = @imagecreatefromstring($rawContent);
        if (!$srcImage) {
            throw new RuntimeException("El archivo proporcionado no es una imagen valida o se encuentra corrupto.");
        }

        // Correccion de orientacion EXIF si es JPEG y exif_read_data esta disponible
        $mimeType = $file->getMimeType();
        if (function_exists('exif_read_data') && ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg')) {
            try {
                $exif = @exif_read_data($realPath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $rotated = imagerotate($srcImage, 180, 0);
                            if ($rotated !== false) {
                                imagedestroy($srcImage);
                                $srcImage = $rotated;
                            }
                            break;
                        case 6:
                            $rotated = imagerotate($srcImage, -90, 0);
                            if ($rotated !== false) {
                                imagedestroy($srcImage);
                                $srcImage = $rotated;
                            }
                            break;
                        case 8:
                            $rotated = imagerotate($srcImage, 90, 0);
                            if ($rotated !== false) {
                                imagedestroy($srcImage);
                                $srcImage = $rotated;
                            }
                            break;
                    }
                }
            } catch (\Throwable $e) {
                // Si la lectura EXIF falla o contiene datos danados, continuar sin rotar
            }
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Redimensionado proporcional si el ancho supera MAX_WIDTH (1280px)
        if ($origWidth > self::MAX_WIDTH) {
            $targetWidth = self::MAX_WIDTH;
            $targetHeight = (int) round(($origHeight / $origWidth) * $targetWidth);

            $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

            // Preservar canal alfa / transparencia
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $targetWidth, $targetHeight, $transparent);

            imagecopyresampled(
                $dstImage,
                $srcImage,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $origWidth,
                $origHeight
            );

            imagedestroy($srcImage);
            $finalImage = $dstImage;
        } else {
            // Asegurar modo truecolor y canal alfa sin redimensionar
            imagepalettetotruecolor($srcImage);
            imagealphablending($srcImage, false);
            imagesavealpha($srcImage, true);
            $finalImage = $srcImage;
        }

        // Generar nombre unico con UUID y extension .webp
        $filename = Str::uuid()->toString() . '.webp';
        $relativeDir = "inventario/{$categoria}";
        $relativePath = "{$relativeDir}/{$filename}";

        // Asegurar que el directorio exista en storage/app/public/
        Storage::disk('public')->makeDirectory($relativeDir);
        $fullPath = Storage::disk('public')->path($relativePath);

        // Exportar a WebP y liberar recursos de memoria
        $saved = imagewebp($finalImage, $fullPath, self::WEBP_QUALITY);
        imagedestroy($finalImage);

        if (!$saved || !file_exists($fullPath)) {
            throw new RuntimeException("Fallo al escribir el archivo WebP en el disco de almacenamiento.");
        }

        return $relativePath;
    }

    /**
     * Ejecuta el guardado o vinculacion de una foto dentro de una transaccion de base de datos.
     * Si la transaccion falla, elimina de inmediato el archivo .webp creado para evitar archivos huerfanos.
     *
     * @param UploadedFile|null $file Archivo nuevo a procesar
     * @param int|null $existingFotoId ID de una foto preexistente a reutilizar
     * @param string $categoria Categoria valida (equipos, mobiliarios, etc.)
     * @param callable $persistCallback Funcion que recibe (Foto $foto) y persiste el articulo
     * @return mixed Resultado retornado por el callback
     * @throws \Throwable
     */
    public function storeWithTransaction(
        ?UploadedFile $file,
        ?int $existingFotoId,
        string $categoria,
        callable $persistCallback
    ) {
        $this->validateCategory($categoria);

        if (!$file && !$existingFotoId) {
            throw new InvalidArgumentException("Debe proporcionar un archivo de imagen o seleccionar una foto existente.");
        }

        $createdPath = null;
        if ($file) {
            $createdPath = $this->storeWebpFile($file, $categoria);
        }

        try {
            return DB::transaction(function () use ($createdPath, $existingFotoId, $persistCallback) {
                if ($createdPath) {
                    $foto = new Foto();
                    $foto->ruta_foto = $createdPath;
                    $foto->save();
                } else {
                    $foto = Foto::findOrFail($existingFotoId);
                }

                return $persistCallback($foto);
            });
        } catch (\Throwable $e) {
            // Limpieza atomica: si se genero un archivo fisico pero la transaccion fallo, borrarlo
            if ($createdPath && Storage::disk('public')->exists($createdPath)) {
                Storage::disk('public')->delete($createdPath);
            }
            throw $e;
        }
    }

    /**
     * Reemplaza la fotografia de un articulo existente de forma segura.
     * Actualiza la clave foranea FOTO_id_foto y elimina la foto anterior si no es utilizada por otro registro.
     *
     * @param Model $articulo Modelo que posee la columna FOTO_id_foto
     * @param UploadedFile $newFile Nuevo archivo de imagen
     * @param string $categoria Categoria del articulo
     * @return Foto Nuevo modelo Foto asociado
     * @throws \Throwable
     */
    public function replaceArticleFoto(Model $articulo, UploadedFile $newFile, string $categoria): Foto
    {
        $oldFotoId = $articulo->FOTO_id_foto;
        $newFoto = null;

        $this->storeWithTransaction(
            $newFile,
            null,
            $categoria,
            function (Foto $foto) use ($articulo, &$newFoto) {
                $articulo->FOTO_id_foto = $foto->id_foto;
                $articulo->save();
                $newFoto = $foto;
                return $articulo;
            }
        );

        // Purgar foto anterior si quedo sin uso
        if ($oldFotoId) {
            $this->deleteFotoIfUnused((int) $oldFotoId);
        }

        return $newFoto;
    }

    /**
     * Elimina el registro de la tabla foto y su archivo fisico en disco
     * unicamente si ningun otro articulo (equipo, mobiliario, material, accesorio) la referencia.
     *
     * @param int|null $fotoId
     * @return bool True si fue eliminada, False si sigue en uso o no existe
     */
    public function deleteFotoIfUnused(?int $fotoId): bool
    {
        if (!$fotoId || $fotoId <= 0) {
            return false;
        }

        $usages = (int) DB::table('equipo')->where('FOTO_id_foto', $fotoId)->count()
                + (int) DB::table('mobiliario')->where('FOTO_id_foto', $fotoId)->count()
                + (int) DB::table('material')->where('FOTO_id_foto', $fotoId)->count()
                + (int) DB::table('accesorio')->where('FOTO_id_foto', $fotoId)->count();

        if ($usages > 0) {
            return false;
        }

        $foto = Foto::find($fotoId);
        if (!$foto) {
            return false;
        }

        $ruta = $foto->ruta_foto;
        if ($ruta) {
            if (Storage::disk('public')->exists($ruta)) {
                Storage::disk('public')->delete($ruta);
            } elseif (file_exists(public_path($ruta))) {
                @unlink(public_path($ruta));
            }
        }

        $foto->delete();
        return true;
    }

    /**
     * Obtiene una coleccion de fotos recientes pertenecientes a una categoria para su reutilizacion en vistas.
     *
     * @param string $categoria
     * @param int $limit Maximo numero de registros a devolver
     * @return Collection
     */
    public function getReusablePhotos(string $categoria, int $limit = 24): Collection
    {
        $this->validateCategory($categoria);

        return Foto::where('ruta_foto', 'like', "inventario/{$categoria}/%")
            ->orderBy('id_foto', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Resuelve la URL publica accesible de una foto, soportando tanto el nuevo esquema
     * (storage/inventario/...) como las rutas heredadas (img/equipos-img/...).
     *
     * @param string|null $rutaFoto
     * @return string|null
     */
    public function resolveUrl(?string $rutaFoto): ?string
    {
        if (empty($rutaFoto)) {
            return null;
        }

        // Si es una ruta almacenada en el disco publico bajo inventario/
        if (Str::startsWith($rutaFoto, 'inventario/') || Str::startsWith($rutaFoto, '/inventario/')) {
            return asset('storage/' . ltrim($rutaFoto, '/'));
        }

        // Si ya incluye el prefijo storage/
        if (Str::startsWith($rutaFoto, 'storage/')) {
            return asset($rutaFoto);
        }

        // Rutas heredadas almacenadas en public/ (ej. img/equipos-img/foto.jpg)
        return asset($rutaFoto);
    }
}
