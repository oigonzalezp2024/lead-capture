"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
function handleLogin() {
    return __awaiter(this, void 0, void 0, function* () {
        // 2. Selección de elementos con Tipado Estricto
        const userField = document.getElementById('username');
        const passField = document.getElementById('password');
        const btn = document.getElementById('login-btn');
        const errorBox = document.getElementById('error-box');
        // Validación de existencia de elementos (Seguridad de Runtime)
        if (!userField || !passField || !btn || !errorBox)
            return;
        const user = userField.value;
        const pass = passField.value;
        if (!user || !pass)
            return;
        // 3. Estado de carga
        btn.innerText = 'Verificando...';
        btn.disabled = true;
        errorBox.style.display = 'none';
        try {
            // Petición al backend
            const response = yield fetch(window.location.pathname, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: user, password: pass })
            });
            const result = yield response.json();
            if (result.status === 'success') {
                // Redirección segura
                window.location.href = './seguimiento/';
            }
            else {
                errorBox.innerText = result.message;
                errorBox.style.display = 'block';
                btn.innerText = 'Iniciar Sesión';
                btn.disabled = false;
            }
        }
        catch (e) {
            errorBox.innerText = 'Error de comunicación con el servidor';
            errorBox.style.display = 'block';
            btn.innerText = 'Iniciar Sesión';
            btn.disabled = false;
        }
    });
}
// 4. Escucha de teclado con tipado de eventos
document.addEventListener('keypress', (e) => {
    if (e.key === 'Enter')
        handleLogin();
});
// 5. Exponer a la ventana global para el onclick del HTML
window.handleLogin = handleLogin;
//# sourceMappingURL=login.js.map