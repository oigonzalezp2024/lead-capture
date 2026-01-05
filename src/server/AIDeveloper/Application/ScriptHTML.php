<?php
namespace App\Server\AIDeveloper\Application;

use Exception;
use App\Server\AIDeveloper\Exceptions\AiGenerationException;
use App\Server\AIDeveloper\Exceptions\FileStorageException;

use App\Server\AIDeveloper\Domain\IAgenteCSS;
use App\Server\AIDeveloper\Domain\IConfigGeminiApi;
use App\Server\AIDeveloper\Domain\ICurlRequest;
use App\Server\AIDeveloper\Domain\IFileManager;
use App\Server\AIDeveloper\Domain\ILeadCaptureGeminiApi;
use App\Server\AIDeveloper\Domain\IOutputClean;
use App\Server\AIDeveloper\Domain\IPromptConstructor;
use App\Server\AIDeveloper\Domain\IScriptl;
use App\Server\AIDeveloper\Domain\Prompt;

use App\Server\AIDeveloper\Infrastructure\Api\ConfigGeminiApi;
use App\Server\AIDeveloper\Infrastructure\Api\LeadCaptureGeminiApi;
use App\Server\AIDeveloper\Infrastructure\Network\CurlRequest;
use App\Server\AIDeveloper\Infrastructure\Storage\FileManager;
use App\Server\AIDeveloper\Infrastructure\Utils\PromptConstructor;
use App\Server\AIDeveloper\Infrastructure\Utils\PromptConstructorHTML;
use App\Server\AIDeveloper\Infrastructure\Utils\OutputClean;
use App\Server\AIDeveloper\Service\AgenteCSS;

class ScriptHTML implements IScriptL
{
    private string $apiKey;
    private string $model;

    public function __construct(string $apiKey, string $model) {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    function cambiaCSS(string $rutaHTML, string $rutaCSS, string $rutaJS, string $sugerencia): string
    {
        $apiKey = $this->apiKey;
        $model = $this->model;

        $fileManager = new FileManager();

        $agenteHTML = new AgenteCSS(
            new LeadCaptureGeminiApi($apiKey, $model),
            new PromptConstructorHTML(
                new Prompt(
                    $fileManager->read($rutaHTML),
                    $fileManager->read($rutaCSS),
                    $fileManager->read($rutaJS),
                    $sugerencia
                )
            ),
            new ConfigGeminiApi(),
            new CurlRequest(),
            new OutputClean()
        );

        $agenteHTML->process();

        $nuevoContenidoHTML = $agenteHTML->output();

        if (!empty($nuevoContenidoHTML)) {
            if ($fileManager->save($rutaHTML, $nuevoContenidoHTML)) {
                return "El HTML ha sido actualizado con éxito.";
            } else {
                return "Error al escribir en el archivo.";
            }
        } else {
            return "No se pudo generar el nuevo contenido.";
        }
    }
}
