<?php
/**
 * Script independiente para el registro del primer administrador.
 * Alimentado por variables de entorno (.env).
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno
try {
    $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
    $dotenv->load();
} catch (Exception $e) {
    die("Error: No se pudo cargar el archivo .env");
}

// 1. Configuración de conexión desde .env
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? 'lead_capture_software';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';

// Credenciales deseadas (puedes definirlas en el .env o dejarlas aquí temporalmente)
$usuario = $_ENV['ADMIN_INIT_USER'] ?? 'admin';
$password_plana = $_ENV['ADMIN_INIT_PASS'] ?? 'admin123';

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 2. Generación del Hash
    $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

    // 3. Inserción/Actualización con lógica de seguridad
    $sql = "INSERT INTO system_users (username, password_hash) 
            VALUES (:user, :pass) 
            ON DUPLICATE KEY UPDATE password_hash = :pass";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user' => $usuario,
        ':pass' => $password_hash
    ]);

    $mensaje = "Administrador configurado con éxito desde el entorno.<br>";
    $mensaje .= "Usuario: <b>$usuario</b><br>";
    $mensaje .= "Contraseña: <b>$password_plana</b><br>";
    $mensaje .= "Origen de DB: <b>$db_host / $db_name</b><br><br>";
    $mensaje .= "<b style='color:red;'>ALERTA: Elimina este archivo inmediatamente.</b>";

} catch (PDOException $e) {
    $mensaje = "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    $mensaje = "Error general: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador de Administrador (.env)</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #eceff1; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 400px; width: 100%; text-align: center; }
        h2 { color: #263238; margin-top: 0; }
        p { color: #546e7a; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Panel de Instalación</h2>
        <p><?php echo $mensaje; ?></p>
        <a href="index.php" class="btn">Ir al Login</a>
    </div>
</body>
</html>