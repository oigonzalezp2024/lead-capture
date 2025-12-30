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

    <script>
        async function loadLeads() {
            const res = await fetch('admin_api.php?action=list');
            const data = await res.json();
            document.getElementById('leads-table').innerHTML = data.map(p => `
                <tr>
                    <td>#${p.id_prospect}</td>
                    <td>${p.created_at}</td>
                    <td><strong>${p.full_name}</strong></td>
                    <td><span class="badge">${p.profile_type}</span></td>
                    <td><button class="view-btn" onclick="showDetail(${p.id_prospect})">Ver Diagnóstico</button></td>
                </tr>
            `).join('');
        }

        async function showDetail(id) {
            const res = await fetch(`admin_api.php?action=detail&id=${id}`);
            const data = await res.json();
            const body = document.getElementById('modal-body');

            let html = `
                <div class="info-section">
                    <h3>Información de Contacto</h3>
                    <p><strong>ID Interno:</strong> #${data.info.id_prospect}</p>
                    <p><strong>Nombre Completo:</strong> ${data.info.full_name}</p>
                    <p><strong>Contacto (WA/Email):</strong> ${data.info.email_whatsapp}</p>
                    <p><strong>Perfil Identificado:</strong> ${data.info.profile_type}</p>
                    <p><strong>Fecha de Envío:</strong> ${data.info.created_at}</p>
                </div>
                <h3>Respuestas Detalladas</h3>
            `;

            html += data.answers.map(a => `
                <div class="answer-item">
                    <label>${a.question_text || 'Código: ' + a.question_key}</label>
                    <div>${a.answer_value}</div>
                </div>
            `).join('');

            body.innerHTML = html;
            document.getElementById('modal').style.display = 'flex';
        }

        function closeModal() { document.getElementById('modal').style.display = 'none'; }
        window.onload = loadLeads;
    </script>
</body>
</html>
