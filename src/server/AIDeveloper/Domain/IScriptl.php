<?php
namespace App\Server\AIDeveloper\Domain;

interface IScriptl
{
    public function cambiaCSS(string $rutaHTML, string $rutaCSS, string $rutaJS, string $sugerencia): string;
}
