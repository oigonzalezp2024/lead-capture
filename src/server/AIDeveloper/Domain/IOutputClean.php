<?php
namespace App\Server\AIDeveloper\Domain;

interface IOutputClean
{
    function input(array $input);
    function process(): void;
    function output(): string;
}
