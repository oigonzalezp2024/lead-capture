<?php
require_once '../../../../vendor/autoload.php';

use App\Server\AIDeveloper\Application\ScriptTS;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Recoger datos de POST
        $apiKey     = $_POST['apiKey'] ?? '';
        $model      = $_POST['model'] ?? 'gemini-1.5-flash';
        $sugerencia = $_POST['sugerencia'] ?? '';
        $rutaHTML   = '../taller/index.html';
        $rutaCSS    = '../taller/style.css';
        $rutaJS     = '../../js/project/admin/taller/taller.js';
        // 2. Validación
        if (
            empty($apiKey) || 
            empty($model) || 
            empty($sugerencia)
            ) {
            throw new Exception("Faltan parámetros obligatorios (apiKey, rutaCSS o sugerencia).");
        }

        // 3. Ejecución
        $script = new ScriptTS($apiKey, $model);
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
