<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../../');
$dotenv->load();

session_start();
header('Content-Type: application/json; charset=utf-8');

// Seguridad: Solo administradores logueados
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Acceso no autorizado"]);
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
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión"]); exit;
}

$action = $_GET['action'] ?? '';

// LISTAR TODOS LOS PROSPECTOS
if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM lead_prospects ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// DETALLE COMPLETO (INFORMACIÓN + RESPUESTAS)
if ($action === 'detail' && isset($_GET['id'])) {
    // Info del prospecto
    $stmtP = $pdo->prepare("SELECT * FROM lead_prospects WHERE id_prospect = ?");
    $stmtP->execute([$_GET['id']]);
    $prospect = $stmtP->fetch(PDO::FETCH_ASSOC);

    // Todas sus respuestas cruzadas con el texto de la pregunta (Insumo Sagrado)
    $stmtA = $pdo->prepare("
        SELECT a.question_key, a.answer_value, q.question_text 
        FROM lead_survey_answers a
        LEFT JOIN lead_survey_questions q ON a.question_key = q.codigo_pregunta
        WHERE a.id_prospect = ?
        ORDER BY q.orden ASC
    ");
    $stmtA->execute([$_GET['id']]);
    $answers = $stmtA->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "info" => $prospect,
        "answers" => $answers
    ]);
}
exit;
