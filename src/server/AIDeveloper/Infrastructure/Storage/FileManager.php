<?php
namespace App\Server\AIDeveloper\Infrastructure\Storage;

use App\Server\AIDeveloper\Domain\IFileManager;

class FileManager implements IFileManager
{
    /**
     * Lee el contenido de un archivo de forma segura.
     * * @param string $path Ruta del archivo.
     * @return string Contenido del archivo o cadena vacía si no es legible.
     */
    public function read(string $path): string
    {
        if (is_file($path) && is_readable($path)) {
            return file_get_contents($path);
        }
        return "";
    }

    /**
     * Actualiza el archivo de forma segura con bloqueo exclusivo.
     * * @param string $path Ruta del archivo.
     * @param string $contenido Nuevo contenido a escribir.
     * @return bool True si la operación fue exitosa.
     */
    public function save(string $path, string $contenido): bool
    {
        if (empty($contenido)) {
            return false;
        }

        // LOCK_EX evita que otros procesos escriban al mismo tiempo
        return file_put_contents($path, $contenido, LOCK_EX) !== false;
    }
}
