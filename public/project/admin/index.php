<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../../');
$dotenv->load();

session_start();

// Redirección si ya hay sesión
if (isset($_SESSION['admin_id'])) {
    header("Location: ./seguimiento/");
    exit;
}

// API de Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Implementación de variables de entorno preservando tu configuración
    $host = $_ENV['DB_HOST'];
    $db   = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $stmt = $pdo->prepare("SELECT id_user, username, password_hash FROM lead_system_users WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id_user'];
            $_SESSION['admin_user'] = $admin['username'];
            
            $pdo->prepare("UPDATE lead_system_users SET last_login = CURRENT_TIMESTAMP WHERE id_user = ?")
                ->execute([$admin['id_user']]);

            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Usuario o contraseña incorrectos"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error de conexión en el servidor"]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo | Lead Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login_style.css">
</head>
<body>

    <div class="login-box">
        <h2>Admin Access</h2>
        <p>Ingresa tus credenciales autorizadas</p>
        
        <div id="error-box" class="error-msg"></div>

        <label for="username">Usuario</label>
        <input type="text" id="username" placeholder="Tu usuario" autocomplete="username">
        
        <label for="password">Contraseña</label>
        <input type="password" id="password" placeholder="••••••••" autocomplete="current-password">
        
        <button onclick="handleLogin()" id="login-btn">Iniciar Sesión</button>
    </div>

    <script type="module" src="../js/project/auth/login.js"></script>
</body>
</html>
