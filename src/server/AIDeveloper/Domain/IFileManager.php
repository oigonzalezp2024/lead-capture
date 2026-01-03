<?php
namespace App\Server\AIDeveloper\Domain;

interface IFileManager
{
    public function read(string $path): string;
    public function save(string $path, string $contenido): bool;
}
