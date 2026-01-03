<?php
namespace App\Server\AIDeveloper\Domain;

interface IConfigGeminiApi
{
    public function setPrompt(string $prompt): void;
    public function setTemperature(float $temperature): void;
    public function setData(): void;
    public function getData(): array;
}
