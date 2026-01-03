<?php

class AiGenerationException extends Exception {}
class FileStorageException extends Exception {}

// domain

interface ILeadCaptureGeminiApi
{
    public function getUrl(): string;
}

interface IConfigGeminiApi
{
    public function setPrompt(string $prompt): void;
    public function setTemperature(float $temperature): void;
    public function setData(): void;
    public function getData(): array;
}

interface IPrompt
{
    public function getHTML(): string;
    public function getCSS(): string;
    public function getJS(): string;
    public function getSugerencia(): string;
}

interface IPromptConstructor
{
    public function process(): void;
    public function output();
}

interface ICurlRequest
{
    public function setUrl(string $url);
    public function setData(array $data);
    public function process(): void;
    public function output(): array|string;
}

interface IOutputClean
{
    function input(array $input);
    function process(): void;
    function output(): string;
}

interface IAgenteCSS
{
    public function process(): void;
    public function output(): string;
}

interface IFileManager
{
    public function read(string $path): string;
    public function save(string $path, string $contenido): bool;
}

interface IScriptl
{
    public function cambiaCSS(string $rutaHTML, string $rutaCSS, string $rutaJS, string $sugerencia): string;
}

class Prompt implements IPrompt
{
    private string $html;
    private string $css;
    private string $js;
    private string $sugerencia;

    public function __construct(
        string $html,
        string $css,
        string $js,
        string $sugerencia
    ) {
        $this->html = $html;
        $this->css = $css;
        $this->js = $js;
        $this->sugerencia = $sugerencia;
    }

    public function getHTML(): string
    {
        return $this->html;
    }

    public function getCSS(): string
    {
        return $this->css;
    }

    public function getJS(): string
    {
        return $this->js;
    }

    public function getSugerencia(): string
    {
        return $this->sugerencia;
    }
}

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

class PromptConstructor implements IPromptConstructor
{
    private IPrompt $prompt;
    private string $output;

    public function __construct(
        IPrompt $prompt
    ) {
        $this->prompt = $prompt;
    }

    public function process(): void
    {
        $html = (string) $this->prompt->getHTML();
        $css = (string) $this->prompt->getCSS();
        $js = (string) $this->prompt->getJS();
        $sugerencia = (string) $this->prompt->getSugerencia();

        $prompt = "Actúa como un experto en UI/UX. 
                CONTEXTO: Se proporciona un HTML que contiene una 'Encuesta Adaptativa para captar clientes de desarrollo de software'. Esta estructura es SAGRADA y no debe verse afectada funcionalmente.
                HTML: '$html'. 
                CSS ACTUAL: '$css'. 
                JS: '$js'. 
                
                TAREA: Modifica el CSS según esta sugerencia: '$sugerencia'. 
                REGLA CRÍTICA: Devuelve ÚNICAMENTE el código CSS resultante. No incluyas explicaciones, ni bloques de código markdown (```css ... ```).";

        $this->output = (string) $prompt;
    }

    public function output()
    {
        return $this->output;
    }
}

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

class CurlRequest implements ICurlRequest
{
    private string $url;
    private array $data;
    private array|string $output = [];

    public function __construct(string $url = "", array $data = [])
    {
        $this->url = $url;
        $this->data = $data;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    function process(): void
    {
        $url = $this->url;
        $data = $this->data;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new AiGenerationException("Error de conexión con la API: " . $error);
        }
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            throw new AiGenerationException("API Error: " . ($result['error']['message'] ?? 'Desconocido'));
        }
        curl_close($ch);
        $this->output = $result;
    }

    function output(): array|string
    {
        return $this->output;
    }
}

class OutputClean implements IOutputClean
{
    private array $input;
    private string $output;

    public function __construct(
        array $input = []
    ) {
        $this->input = $input;
    }

    function input(array $input)
    {
        $this->input = $input;
    }

    function process(): void
    {
        $input = $this->input;
        $textoFinal = $input['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$textoFinal) {
            $this->output = "";
            return;
        }

        $textoFinal = preg_replace('/^```[a-z]*\s*|\s*```$/i', '', $textoFinal);
        $this->output = trim($textoFinal);
    }

    function output(): string
    {
        return $this->output;
    }
}

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

class FileManager implements IFileManager
{
    /**
     * Lee el contenido de un archivo de forma segura.
     * * @param string $path Ruta del archivo.
     * @return string Contenido del archivo o cadena vacía si no es legible.
     */
    public function read(string $path): string
    {
        if (is_file($path) && is_readable($path)) {
            return file_get_contents($path);
        }
        return "";
    }

    /**
     * Actualiza el archivo de forma segura con bloqueo exclusivo.
     * * @param string $path Ruta del archivo.
     * @param string $contenido Nuevo contenido a escribir.
     * @return bool True si la operación fue exitosa.
     */
    public function save(string $path, string $contenido): bool
    {
        if (empty($contenido)) {
            return false;
        }

        // LOCK_EX evita que otros procesos escriban al mismo tiempo
        return file_put_contents($path, $contenido, LOCK_EX) !== false;
    }
}

class Script implements IScriptl
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

        $agenteCSS = new AgenteCSS(
            new LeadCaptureGeminiApi($apiKey, $model),
            new PromptConstructor(
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

        $agenteCSS->process();

        $nuevoContenidoCSS = $agenteCSS->output();

        if (!empty($nuevoContenidoCSS)) {
            if ($fileManager->save($rutaCSS, $nuevoContenidoCSS)) {
                return "El CSS ha sido actualizado con éxito.";
            } else {
                return "Error al escribir en el archivo.";
            }
        } else {
            return "No se pudo generar el nuevo contenido.";
        }
    }
}

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
