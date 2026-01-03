<?php
namespace App\Server\AIDeveloper\Service;

use App\Server\AIDeveloper\Domain\ILeadCaptureGeminiApi;
use App\Server\AIDeveloper\Domain\IPromptConstructor;
use App\Server\AIDeveloper\Domain\IConfigGeminiApi;
use App\Server\AIDeveloper\Domain\ICurlRequest;
use App\Server\AIDeveloper\Domain\IOutputClean;
use App\Server\AIDeveloper\Domain\IAgenteCSS;

class AgenteCSS implements IAgenteCSS
{
    private ILeadCaptureGeminiApi $api;
    private IPromptConstructor $promptConstructor;
    private IConfigGeminiApi $configGeminiApi;
    private ICurlRequest $curlRequest;
    private IOutputClean $outputClean;
    private string $output = "";

    public function __construct(
        ILeadCaptureGeminiApi $api,
        IPromptConstructor $promptConstructor,
        IConfigGeminiApi $configGeminiApi,
        ICurlRequest $curlRequest,
        IOutputClean $outputClean
    ) {
        $this->api = $api;
        $this->promptConstructor = $promptConstructor;
        $this->configGeminiApi = $configGeminiApi;
        $this->curlRequest = $curlRequest;
        $this->outputClean = $outputClean;
    }

    public function process(): void
    {
        // 1. Obtener URL y construir Prompt
        $url = $this->api->getUrl();
        $this->promptConstructor->process();
        $promptText = $this->promptConstructor->output();

        // 2. Configurar JSON para la API
        $this->configGeminiApi->setPrompt($promptText);
        $this->configGeminiApi->setTemperature(0.2);
        $this->configGeminiApi->setData();
        $data = $this->configGeminiApi->getData();

        // 3. Ejecutar Petición Curl
        $this->curlRequest->setUrl($url);
        $this->curlRequest->setData($data);
        $this->curlRequest->process();
        $result = $this->curlRequest->output();

        // 4. Limpiar Salida
        if (is_array($result)) {
            $this->outputClean->input($result);
            $this->outputClean->process();
            $this->output = $this->outputClean->output();
        }
    }

    public function output(): string
    {
        return $this->output;
    }
}
