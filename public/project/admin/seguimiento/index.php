<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo - Leads</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../admin_style.css">
</head>
<body>
    <aside class="sidebar">
        <h2>SISTEMA ADMIN</h2>
        <p>Usuario: <strong><?php echo $_SESSION['admin_user']; ?></strong></p>
        <nav>
            <a href="../seguimiento/" class="nav-link active">📊 Seguimiento</a>
            <a href="../configuracion/" class="nav-link">⚙️ Configuración</a>
            <a href="../preguntas/" class="nav-link">⚙️ Preguntas</a>
            <a href="../../encuesta/" class="nav-link">✅ Encuesta</a>
            <a href="../logout.php" class="nav-link" style="margin-top:2rem; color:#f87171">Cerrar Sesión</a>
        </nav>
    </aside>

    <div class="content">
        <h1>Prospectos Registrados</h1>
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombre / Empresa</th>
                    <th>Perfil</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="leads-table"></tbody>
        </table>
    </div>

    <div id="modal" class="modal-overlay" onclick="closeModal()">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2>Detalle Completo del Diagnóstico</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <div id="modal-body" class="modal-body">
                </div>
        </div>
    </div>

    <script type="module" src="../../js/project/admin/seguimiento/seguimiento.js"></script>
</body>
</html>
