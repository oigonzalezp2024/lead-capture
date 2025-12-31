<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ .'/../../../');
$dotenv->load();

header('Content-Type: application/json; charset=utf-8');

// Implementación de variables de entorno conservando tu configuración de PDO
$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS']; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Conexión fallida: " . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// GET: Cargar configuración de la encuesta
if ($method === 'GET') {
    try {
        $questions = $pdo->query("SELECT * FROM lead_survey_questions ORDER BY orden ASC")->fetchAll();
        $options = $pdo->query("SELECT * FROM lead_question_options")->fetchAll();

        foreach ($questions as &$q) {
            $q['options'] = array_values(array_filter($options, fn($opt) => $opt['id_question'] == $q['id_question']));
        }
        echo json_encode($questions);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// POST: Guardar respuestas del prospecto
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        echo json_encode(["status" => "error", "message" => "No se recibieron datos"]);
        exit;
    }

    $pdo->beginTransaction();
    try {
        // 1. Insertar en tabla maestra 'lead_prospects'
        $stmtLead = $pdo->prepare("INSERT INTO lead_prospects (full_name, email_whatsapp, profile_type) VALUES (?, ?, ?)");
        $stmtLead->execute([
            $data['answers']['F1'] ?? 'Sin nombre',
            $data['answers']['F2'] ?? 'Sin contacto',
            $data['profile_type']
        ]);
        
        $idProspect = $pdo->lastInsertId();

        // 2. Insertar cada respuesta en 'lead_survey_answers'
        $stmtAns = $pdo->prepare("INSERT INTO lead_survey_answers (id_prospect, question_key, answer_value) VALUES (?, ?, ?)");
        foreach ($data['answers'] as $key => $val) {
            // Evitamos duplicar datos de contacto en la tabla de respuestas si no es necesario
            if (!in_array($key, ['F1', 'F2'])) {
                $stmtAns->execute([$idProspect, $key, $val]);
            }
        }

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Registro completado con ID: $idProspect"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
