<?php
namespace App\Server\AIDeveloper\Domain;

interface IPrompt
{
    public function getHTML(): string;
    public function getCSS(): string;
    public function getJS(): string;
    public function getSugerencia(): string;
}
