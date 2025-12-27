<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración | Lead System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../admin_style.css">
</head>
<body>
    <aside class="sidebar">
        <h2>SISTEMA ADMIN</h2>
        <p>Usuario: <strong><?php echo $_SESSION['admin_user']; ?></strong></p>
        <nav>
            <a href="../seguimiento/" class="nav-link">📊 Seguimiento</a>
            <a href="../configuracion/" class="nav-link active">⚙️ Configuración</a>
            <a href="../../encuesta/" class="nav-link">✅ Encuesta</a>
            <a href="../logout.php" class="nav-link" style="margin-top:2rem; color:#f87171">Cerrar Sesión</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="header">
            <div>
                <h1 style="margin:0">Configuración de Encuesta</h1>
                <p style="color:var(--text-dim); margin:5px 0 0 0">Gestione la estructura del Insumo Sagrado.</p>
            </div>
            <button class="btn btn-primary" onclick="openCompModal()">+ Nuevo Componente</button>
        </header>

        <div id="main-list" class="grid"></div>
    </main>

    <div id="modal" class="modal-overlay" onclick="this.style.display='none'">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem">
                <h2 id="m-title" style="margin:0">Editor</h2>
                <button onclick="document.getElementById('modal').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer">&times;</button>
            </div>
            <div id="m-body"></div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
