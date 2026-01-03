<?php
require_once '../../../../vendor/autoload.php';

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
use App\Server\AIDeveloper\Infrastructure\Utils\OutputClean;
use App\Server\AIDeveloper\Service\AgenteCSS;
use App\Server\AIDeveloper\Application\Script;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Recoger datos de POST
        $apiKey     = $_POST['apiKey'] ?? '';
        $model      = $_POST['model'] ?? 'gemini-1.5-flash';
        $sugerencia = $_POST['sugerencia'] ?? '';
        $rutaHTML   = './index.html';
        $rutaCSS    = './style.css';
        $rutaJS     = './script.js';
        // 2. Validación
        if (
            empty($apiKey) || 
            empty($model) || 
            empty($sugerencia)
            ) {
            throw new Exception("Faltan parámetros obligatorios (apiKey, rutaCSS o sugerencia).");
        }

        // 3. Ejecución
        $script = new Script($apiKey, $model);
        $resultMessage = $script->cambiaCSS($rutaHTML, $rutaCSS, $rutaJS, $sugerencia);

        // 4. Respuesta Exitosa
        echo json_encode([
            "status"  => "success",
            "message" => $resultMessage,
            "data"    => [
                "model"   => $model,
                "file"    => basename($rutaCSS),
                "version" => time() // <--- Agregamos el timestamp actual
            ]
        ], JSON_PRETTY_PRINT);

    } catch (Exception $e) {
        // 5. Respuesta de Error
        http_response_code(400); // Bad Request
        echo json_encode([
            "status"  => "error",
            "message" => $e->getMessage()
        ], JSON_PRETTY_PRINT);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status"  => "error",
        "message" => "Solo se permiten peticiones POST."
    ]);
}
