<?php
namespace App\Server\AIDeveloper\Domain;

interface IPromptConstructor
{
    public function process(): void;
    public function output();
}
