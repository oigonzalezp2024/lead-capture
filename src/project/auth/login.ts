// 1. Interfaz para la respuesta del servidor
interface LoginResponse {
    status: 'success' | 'error';
    message: string;
}

async function handleLogin(): Promise<void> {
    // 2. Selección de elementos con Tipado Estricto
    const userField = document.getElementById('username') as HTMLInputElement | null;
    const passField = document.getElementById('password') as HTMLInputElement | null;
    const btn = document.getElementById('login-btn') as HTMLButtonElement | null;
    const errorBox = document.getElementById('error-box') as HTMLDivElement | null;

    // Validación de existencia de elementos (Seguridad de Runtime)
    if (!userField || !passField || !btn || !errorBox) return;

    const user = userField.value;
    const pass = passField.value;

    if (!user || !pass) return;

    // 3. Estado de carga
    btn.innerText = 'Verificando...';
    btn.disabled = true;
    errorBox.style.display = 'none';

    try {
        // Petición al backend
        const response = await fetch(window.location.pathname, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: user, password: pass })
        });

        const result: LoginResponse = await response.json();

        if (result.status === 'success') {
            // Redirección segura
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

// 4. Escucha de teclado con tipado de eventos
document.addEventListener('keypress', (e: KeyboardEvent) => {
    if (e.key === 'Enter') handleLogin();
});

// 5. Exponer a la ventana global para el onclick del HTML
(window as any).handleLogin = handleLogin;
