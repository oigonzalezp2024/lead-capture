var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
// 2. Estado Global Tipado
let compsCache = [];
let currentRoute = "";
// ... (manten las interfaces y variables del inicio igual)
// AGREGA ESTAS FUNCIONES QUE FALTABAN O TENÍAN ERRORES DE NOMBRE:
function openQuesModal() {
    const title = document.getElementById('m-title');
    const body = document.getElementById('m-body');
    if (title)
        title.innerText = "Nueva Pregunta";
    if (body) {
        body.innerHTML = `
            <label>Código</label><input type="text" id="q_c" placeholder="PREG_EJEMPLO">
            <label>Texto</label><textarea id="q_t" rows="3"></textarea>
            <label>Orden</label><input type="number" id="q_o" value="1">
            <button class="btn btn-primary" style="width:100%" onclick="saveQues()">Guardar</button>`;
    }
}
function openEditQuesModal(codigo, texto, orden) {
    const title = document.getElementById('m-title');
    const body = document.getElementById('m-body');
    if (title)
        title.innerText = "Editar Pregunta";
    if (body) {
        body.innerHTML = `
            <label>Código</label><input type="text" id="q_c" value="${codigo}" readonly style="background:#f8fafc">
            <label>Texto</label><textarea id="q_t" rows="4">${decodeURIComponent(texto)}</textarea>
            <label>Orden</label><input type="number" id="q_o" value="${orden}">
            <button class="btn btn-primary" style="width:100%" onclick="saveQues()">Actualizar</button>
            <button class="btn btn-outline" style="width:100%; margin-top:10px" onclick="loadQues('${currentRoute}')">Cancelar</button>`;
    }
}
function saveComp() {
    return __awaiter(this, void 0, void 0, function* () {
        const idEl = document.getElementById('c_id');
        const parentEl = document.getElementById('c_p');
        const labelEl = document.getElementById('c_l');
        const routeEl = document.getElementById('c_r');
        const b = {
            id: idEl.value || null,
            id_parent: parentEl.value,
            label: labelEl.value,
            route: routeEl.value
        };
        yield api('save_comp', 'POST', b);
        const modal = document.getElementById('modal');
        if (modal)
            modal.style.display = 'none';
        loadComps();
    });
}
function del(id, type) {
    return __awaiter(this, void 0, void 0, function* () {
        if (!confirm("¿Eliminar?"))
            return;
        yield api('delete', 'POST', { id, type });
        type === 'comp' ? loadComps() : loadQues(currentRoute);
    });
}
// Ahora estas líneas ya no darán error porque las funciones existen arriba:
window.openQuesModal = openQuesModal;
window.openEditQuesModal = openEditQuesModal;
window.saveComp = saveComp;
window.del = del;
// 3. Función API Genérica con Tipado Dinámico
function api(act_1) {
    return __awaiter(this, arguments, void 0, function* (act, method = 'GET', body = null) {
        const opt = {
            method,
            headers: { 'Content-Type': 'application/json' }
        };
        if (body)
            opt.body = JSON.stringify(body);
        const res = yield fetch(`config_api.php?action=${act}`, opt);
        return res.json();
    });
}
// 4. Carga de Componentes (Botones)
function loadComps() {
    return __awaiter(this, void 0, void 0, function* () {
        compsCache = yield api('list_comps');
        const container = document.getElementById('main-list');
        if (container) {
            container.innerHTML = compsCache.map(c => `
            <div class="card">
                <h3 style="margin-top:0">${escapeHTML(c.option_label)}</h3>
                <p style="font-size:0.85rem; color:var(--text-dim)">Ruta Destino: <strong>${c.next_route_map}</strong></p>
                <div style="display:flex; gap:8px; margin-top:1.5rem">
                    <button class="btn btn-outline" style="flex:1" onclick="loadQues('${c.next_route_map}')">Preguntas</button>
                    <button class="btn btn-outline" onclick="openCompModal(${c.id_option})">✎</button>
                    <button class="btn btn-danger-soft" onclick="del(${c.id_option}, 'comp')">✕</button>
                </div>
            </div>`).join('');
        }
    });
}
// 5. Carga de Preguntas de una Ruta
function loadQues(route) {
    return __awaiter(this, void 0, void 0, function* () {
        currentRoute = route;
        const ques = yield api(`list_ques&route=${route}`);
        const title = document.getElementById('m-title');
        const body = document.getElementById('m-body');
        const modal = document.getElementById('modal');
        if (title)
            title.innerText = "Ruta: " + route;
        let html = `<button class="btn btn-primary" style="width:100%; margin-bottom:1.5rem" onclick="openQuesModal()">+ Nueva Pregunta</button>`;
        html += ques.map(q => `
        <div style="padding:1rem; border:1px solid var(--border); border-radius:10px; margin-bottom:1rem">
            <div style="display:flex; justify-content:space-between; align-items:center">
                <strong>${q.orden}. ${q.codigo_pregunta}</strong>
                <div>
                    <button class="btn btn-outline" style="padding:4px 8px; font-size:0.7rem" onclick="openEditQuesModal('${q.codigo_pregunta}', '${encodeURIComponent(q.question_text)}', ${q.orden})">EDITAR</button>
                    <button class="btn btn-danger-soft" style="padding:4px 8px; font-size:0.7rem" onclick="del('${q.codigo_pregunta}', 'ques')">BORRAR</button>
                </div>
            </div>
            <p style="margin:0.5rem 0 0 0; font-size:0.9rem">${escapeHTML(q.question_text)}</p>
        </div>`).join('');
        if (body)
            body.innerHTML = html || '<p>No hay preguntas.</p>';
        if (modal)
            modal.style.display = 'flex';
    });
}
// 6. Funciones de Guardado con Captura de Elementos Segura
function saveQues() {
    return __awaiter(this, void 0, void 0, function* () {
        const codeEl = document.getElementById('q_c');
        const textEl = document.getElementById('q_t');
        const orderEl = document.getElementById('q_o');
        const b = {
            codigo: codeEl.value,
            route: currentRoute,
            texto: textEl.value,
            orden: orderEl.value
        };
        yield api('save_ques', 'POST', b);
        loadQues(currentRoute);
    });
}
// 7. Utilidad de Seguridad (Anti-XSS)
export function escapeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
function openCompModal(id = null) {
    const d = id ? compsCache.find(x => x.id_option == id) : null;
    const title = document.getElementById('m-title');
    const body = document.getElementById('m-body');
    const modal = document.getElementById('modal');
    if (title)
        title.innerText = d ? "Editar Botón" : "Nuevo Botón";
    if (body) {
        body.innerHTML = `
            <input type="hidden" id="c_id" value="${id || ''}">
            <label>ID Pregunta Padre</label><input type="number" id="c_p" value="${d ? d.id_question : 1}">
            <label>Texto del Botón</label><input type="text" id="c_l" value="${d ? d.option_label : ''}">
            <label>Ruta Destino</label><input type="text" id="c_r" value="${d ? d.next_route_map : ''}">
            <button class="btn btn-primary" style="width:100%" onclick="saveComp()">Guardar</button>`;
    }
    if (modal)
        modal.style.display = 'flex';
}
// Globalizar funciones para que el HTML pueda verlas (necesario si usas type="module")
window.loadQues = loadQues;
window.openQuesModal = openQuesModal;
window.openEditQuesModal = openEditQuesModal;
window.saveQues = saveQues;
window.del = del;
window.openCompModal = openCompModal;
window.saveComp = saveComp;
window.onload = () => loadComps();
//# sourceMappingURL=configuracion.js.map