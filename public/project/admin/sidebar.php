<?php
function sidebar()
{
?>
    <aside class="sidebar">
        <h2>SISTEMA ADMIN</h2>
        <p>Usuario: <strong><?php echo $_SESSION['admin_user']; ?></strong></p>
        <nav>
            <a href="../seguimiento/" class="nav-link">📊 Seguimiento</a>
            <a href="../preguntas/" class="nav-link">⚙️ Preguntas</a>
            <a href="../configuracion/" class="nav-link active">⚙️ Configuración</a>
            <a href="../AIDeveloperHTML/" class="nav-link">⚙️ AIDeveloperHTML</a>
            <a href="../AIDeveloperCSS/" class="nav-link">⚙️ AIDeveloperCSS</a>
            <a href="../AIDeveloperTS/" class="nav-link">⚙️ AIDeveloperTS</a>
            <a href="../taller/" class="nav-link">⚙️ taller</a>
            <a href="../../encuesta/" class="nav-link">✅ Encuesta</a>
            <a href="../logout.php" class="nav-link" style="margin-top:2rem; color:#f87171">Cerrar Sesión</a>
        </nav>
    </aside>
<?php
}
