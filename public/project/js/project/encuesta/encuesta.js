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
// --- ESTADO DE LA ENCUESTA ---
let config = [];
let currentIndex = 0;
let userRoute = null;
let answers = {};
function startSurvey() {
    return __awaiter(this, void 0, void 0, function* () {
        try {
            const res = yield fetch('api.php');
            if (!res.ok)
                throw new Error("Error en red");
            config = yield res.json();
            renderCurrentQuestion();
        }
        catch (err) {
            const app = document.getElementById('app');
            if (app)
                app.innerHTML = "Error cargando la encuesta.";
        }
    });
}
function renderCurrentQuestion() {
    const container = document.getElementById('app');
    if (!container)
        return;
    const q = config[currentIndex];
    // Final de la encuesta
    if (!q) {
        submitSurvey();
        return;
    }
    // LÓGICA DE RUTEO SAGRADA: Salto de preguntas no pertinentes
    if (q.route !== 'COMMON' && q.route !== userRoute) {
        currentIndex++;
        renderCurrentQuestion();
        return;
    }
    // Barra de progreso con tipado numérico
    const fill = document.getElementById('fill');
    if (fill) {
        const progress = ((currentIndex + 1) / config.length) * 100;
        fill.style.width = `${progress}%`;
    }
    container.innerHTML = `<h2>${q.question_text}</h2>`;
    if (q.question_type === 'choice') {
        q.options.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'opt-btn';
            btn.innerText = opt.option_label;
            btn.onclick = () => {
                if (opt.next_route_map)
                    userRoute = opt.next_route_map;
                answers['profile_type'] = opt.option_value;
                currentIndex++;
                renderCurrentQuestion();
            };
            container.appendChild(btn);
        });
    }
    else {
        const input = q.question_type === 'textarea'
            ? document.createElement('textarea')
            : document.createElement('input');
        input.id = 'current-input';
        container.appendChild(input);
        const nextBtn = document.createElement('button');
        nextBtn.className = 'next-btn';
        nextBtn.innerText = 'Siguiente';
        nextBtn.onclick = () => {
            const inputEl = document.getElementById('current-input');
            const val = inputEl.value.trim();
            if (!val) {
                alert("Este campo es necesario.");
                return;
            }
            answers[q.codigo_pregunta] = val;
            currentIndex++;
            renderCurrentQuestion();
        };
        container.appendChild(nextBtn);
        input.focus();
    }
}
function submitSurvey() {
    return __awaiter(this, void 0, void 0, function* () {
        const container = document.getElementById('app');
        if (container)
            container.innerHTML = "<h2>Guardando información...</h2>";
        const payload = {
            profile_type: answers['profile_type'] || 'UNDEFINED',
            answers: answers
        };
        try {
            const res = yield fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = yield res.json();
            if (container) {
                if (result.status === 'success') {
                    container.innerHTML = "<h2>¡Gracias! Diagnóstico enviado correctamente.</h2>";
                }
                else {
                    throw new Error(result.message || "Error desconocido");
                }
            }
        }
        catch (err) {
            if (container)
                container.innerHTML = `<h2>Error: ${err.message}</h2>`;
        }
    });
}
// Inicialización
document.addEventListener('DOMContentLoaded', startSurvey);
//# sourceMappingURL=encuesta.js.map