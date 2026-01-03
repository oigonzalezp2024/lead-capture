<?php
namespace App\Server\AIDeveloper\Infrastructure\API;

use App\Server\AIDeveloper\Domain\ILeadCaptureGeminiApi;

class LeadCaptureGeminiApi implements ILeadCaptureGeminiApi
{
    private string $apiKey;
    private string $model;
    private string $url;

    public function __construct(
        string $apiKey,
        string $model
    ) {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    private function getApikey(): string
    {
        return $this->apiKey;
    }

    private function getModel(): string
    {
        return $this->model;
    }

    public function setUrl(): void
    {
        $model = $this->getModel();
        $apiKey = $this->getApikey();
        $this->url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    }

    public function getUrl(): string
    {
        $this->setUrl();
        return $this->url;
    }
}
