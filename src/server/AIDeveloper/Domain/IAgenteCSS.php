<?php
namespace App\Server\AIDeveloper\Domain;

interface IAgenteCSS
{
    public function process(): void;
    public function output(): string;
}
