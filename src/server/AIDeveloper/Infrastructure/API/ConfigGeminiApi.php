<?php
namespace App\Server\AIDeveloper\Infrastructure\API;

use App\Server\AIDeveloper\Domain\IConfigGeminiApi;

class ConfigGeminiApi implements IConfigGeminiApi
{
    private string $prompt;
    private array $data;
    private float $temperature;

    public function __construct(
        string $prompt = "",
        float $temperature = 2.0
    ) {
        $this->prompt = $prompt;
        $this->temperature = $temperature;
        $this->setData();
    }

    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }

    public function setTemperature(float $temperature): void
    {
        $this->temperature = $temperature;
    }

    public function setData(): void
    {
        $this->data = [
            "contents" => [
                ["parts" => [["text" => $this->prompt]]]
            ],
            "generationConfig" => [
                "temperature" => $this->temperature,
                "responseMimeType" => "text/plain"
            ]
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }
}
