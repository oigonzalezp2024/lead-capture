async function procesarCambios() {
    const btn = document.getElementById('btnGenerar');
    const loader = document.getElementById('loader');
    const btnText = btn.querySelector('.btn-text');
    const statusBox = document.getElementById('statusMessage');

    const apiKey = document.getElementById('apiKey').value;
    const model = document.getElementById('model').value;
    const sugerencia = document.getElementById('sugerencia').value;

    if (!apiKey || !sugerencia) {
        mostrarEstado("Por favor, rellena los campos obligatorios.", "error");
        return;
    }

    // Bloquear UI
    btn.disabled = true;
    loader.style.display = "block";
    btnText.style.display = "none";
    statusBox.classList.add('hidden');

    const formData = new FormData();
    formData.append('apiKey', apiKey);
    formData.append('model', model);
    formData.append('sugerencia', sugerencia);

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
            mostrarEstado(result.message + " Recargando estilos...", "success");

            // Esperar 2 segundos para que el usuario vea el mensaje de éxito
            setTimeout(() => {
                /**
                 * RECARGA FORZADA CON CACHE BUSTING:
                 * Añadimos un timestamp (?t=...) a la URL actual para que el navegador
                 * se vea obligado a pedir todo de nuevo al servidor.
                 */
                const urlActual = window.location.href.split('?')[0];
                window.location.href = urlActual + '?t=' + new Date().getTime();
            }, 2000);

        } else {
            mostrarEstado(result.message || "Ocurrió un error inesperado.", "error");
        }
    } catch (error) {
        mostrarEstado("Error de conexión con el servidor.", "error");
    } finally {
        // Desbloquear UI por si algo falla
        btn.disabled = false;
        loader.style.display = "none";
        btnText.style.display = "block";
    }
}

function mostrarEstado(mensaje, tipo) {
    const statusBox = document.getElementById('statusMessage');
    statusBox.textContent = mensaje;
    statusBox.className = `status-box ${tipo}`;
}
