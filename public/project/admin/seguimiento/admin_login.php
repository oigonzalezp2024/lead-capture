<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ .'/../../../');
$dotenv->load();

session_start();

// Si ya está logueado, redirigir directamente al panel
if (isset($_SESSION['admin_id'])) {
    header("Location: ./index.php");
    exit;
}

// Procesamiento de la petición de Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Configuración mediante variables de entorno
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

        // Buscar el administrador por nombre de usuario
        $stmt = $pdo->prepare("SELECT id_user, username, password_hash FROM lead_system_users WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validar contraseña usando hash seguro
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id_user'];
            $_SESSION['admin_user'] = $admin['username'];
            
            // Actualizar último login (opcional)
            $pdo->prepare("UPDATE lead_system_users SET last_login = CURRENT_TIMESTAMP WHERE id_user = ?")
                ->execute([$admin['id_user']]);

            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Usuario o contraseña incorrectos"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error de conexión en el módulo"]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo</title>
    <style>
        /* Estilos específicos para independencia total del módulo */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 { color: #1a237e; margin-bottom: 10px; }
        p { color: #666; font-size: 14px; margin-bottom: 30px; }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #1a237e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover { background-color: #283593; }
        .error-msg { color: #d32f2f; font-size: 14px; margin-bottom: 20px; display: none; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Access</h2>
    <p>Ingresa tus credenciales autorizadas</p>
    
    <div id="error-box" class="error-msg"></div>

    <input type="text" id="username" placeholder="Nombre de usuario" required>
    <input type="password" id="password" placeholder="Contraseña" required>
    
    <button onclick="handleLogin()" id="login-btn">Iniciar Sesión</button>
</div>

<script>
    async function handleLogin() {
        const user = document.getElementById('username').value;
        const pass = document.getElementById('password').value;
        const btn = document.getElementById('login-btn');
        const errorBox = document.getElementById('error-box');

        if(!user || !pass) return;

        btn.innerText = 'Verificando...';
        btn.disabled = true;
        errorBox.style.display = 'none';

        try {
            const response = await fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: user, password: pass })
            });

            const result = await response.json();

            if (result.status === 'success') {
                window.location.href = './index.php';
            } else {
                errorBox.innerText = result.message;
                errorBox.style.display = 'block';
                btn.innerText = 'Iniciar Sesión';
                btn.disabled = false;
            }
        } catch (e) {
            errorBox.innerText = 'Error de comunicación con el servidor';
            errorBox.style.display = 'block';
            btn.disabled = false;
        }
    }

    // Permitir login con la tecla Enter
    document.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') handleLogin();
    });
</script>

</body>
</html>
