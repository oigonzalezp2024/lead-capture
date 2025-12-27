<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../../');
$dotenv->load();

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// Implementación de variables de entorno preservando tu lógica original
$host = $_ENV['DB_HOST']; 
$db   = $_ENV['DB_NAME']; 
$user = $_ENV['DB_USER']; 
$pass = $_ENV['DB_PASS'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) { echo json_encode(["error" => "Error DB"]); exit; }

$action = $_GET['action'] ?? '';

// --- LISTAR COMPONENTES (BOTONES) ---
if ($action === 'list_comps') {
    $stmt = $pdo->query("SELECT * FROM lead_question_options WHERE visible = 1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// --- GUARDAR O EDITAR COMPONENTE ---
if ($action === 'save_comp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO lead_question_options (id_option, id_question, option_label, option_value, next_route_map, visible) 
        VALUES (?,?,?,?,?,1) ON DUPLICATE KEY UPDATE option_label=VALUES(option_label), next_route_map=VALUES(next_route_map), visible=1");
    $stmt->execute([$d['id'] ?? null, $d['id_parent'], $d['label'], strtolower($d['label']), strtoupper($d['route'])]);
    echo json_encode(["status" => "success"]);
}

// --- LISTAR PREGUNTAS POR RUTA ---
if ($action === 'list_ques') {
    $stmt = $pdo->prepare("SELECT * FROM lead_survey_questions WHERE route = ? AND visible = 1 ORDER BY orden ASC");
    $stmt->execute([$_GET['route']]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// --- GUARDAR O EDITAR PREGUNTA ---
if ($action === 'save_ques' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO lead_survey_questions (codigo_pregunta, route, question_text, question_type, orden, visible) 
        VALUES (?,?,?,?,?,1) ON DUPLICATE KEY UPDATE question_text=VALUES(question_text), orden=VALUES(orden), visible=1");
    $stmt->execute([$d['codigo'], strtoupper($d['route']), $d['texto'], 'text', $d['orden']]);
    echo json_encode(["status" => "success"]);
}

// --- ELIMINAR (BORRADO LÓGICO) ---
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    $table = $d['type'] === 'comp' ? 'lead_question_options' : 'lead_survey_questions';
    $pk = $d['type'] === 'comp' ? 'id_option' : 'codigo_pregunta';
    $stmt = $pdo->prepare("UPDATE $table SET visible = 0 WHERE $pk = ?");
    $stmt->execute([$d['id']]);
    echo json_encode(["status" => "success"]);
}
