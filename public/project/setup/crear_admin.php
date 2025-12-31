<?php
/**
 * INSTALADOR DE ADMINISTRADOR
 * Ubicación: /setup/crear_admin.php
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;

// 1. Cargar variables de entorno (Un nivel arriba)
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
} catch (Exception $e) {
    die("Error: No se encontró el archivo .env en la raíz.");
}

// 2. Configuración de conexión
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? 'lead_capture_software';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // CANDADO DE SEGURIDAD: Verificar si ya existe un admin
    $check = $pdo->query("SELECT COUNT(*) FROM lead_system_users")->fetchColumn();
    if ($check > 0 && !isset($_GET['force'])) {
        die("El sistema ya tiene usuarios registrados. Por seguridad, elimina la carpeta /setup/ o contacta al soporte.");
    }

    // 3. Credenciales (Prioriza .env, sino usa defaults)
    $usuario = $_ENV['ADMIN_INIT_USER'] ?? 'admin';
    $password_plana = $_ENV['ADMIN_INIT_PASS'] ?? 'admin123';
    $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

    // 4. Inserción
    $sql = "INSERT INTO lead_system_users (username, password_hash) 
            VALUES (:user, :pass) 
            ON DUPLICATE KEY UPDATE password_hash = :pass";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user' => $usuario, ':pass' => $password_hash]);

    $mensaje = "✅ Administrador configurado con éxito.<br>";
    $mensaje .= "Usuario: <b>$usuario</b><br>";
    $mensaje .= "Password: <b>$password_plana</b><br><br>";
    $mensaje .= "<span style='color:red;'>⚠️ <b>ACCIÓN REQUERIDA:</b> Elimina la carpeta <code>/setup/</code> ahora mismo.</span>";

} catch (PDOException $e) {
    $mensaje = "❌ Error de BD: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Setup - Lead Capture Software</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 450px; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 25px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Instalación Inicial</h2>
        <p><?php echo $mensaje; ?></p>
        <a href="../index.php" class="btn">Ir al Inicio de Sesión</a>
    </div>
</body>
</html>