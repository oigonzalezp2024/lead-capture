/* login_script.js */
async function handleLogin() {
    const userField = document.getElementById('username');
    const passField = document.getElementById('password');
    const btn = document.getElementById('login-btn');
    const errorBox = document.getElementById('error-box');

    const user = userField.value;
    const pass = passField.value;

    if (!user || !pass) return;

    // Estado de carga
    btn.innerText = 'Verificando...';
    btn.disabled = true;
    errorBox.style.display = 'none';

    try {
        // Se envía la petición al archivo PHP actual
        const response = await fetch(window.location.pathname, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: user, password: pass })
        });

        const result = await response.json();

        if (result.status === 'success') {
            window.location.href = './seguimiento/';
        } else {
            errorBox.innerText = result.message;
            errorBox.style.display = 'block';
            btn.innerText = 'Iniciar Sesión';
            btn.disabled = false;
        }
    } catch (e) {
        errorBox.innerText = 'Error de comunicación con el servidor';
        errorBox.style.display = 'block';
        btn.innerText = 'Iniciar Sesión';
        btn.disabled = false;
    }
}

// Escuchar tecla Enter para mayor comodidad del usuario
document.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') handleLogin();
});
