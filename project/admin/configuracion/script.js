let compsCache = [];
let currentRoute = "";

async function api(act, method = 'GET', body = null) {
    const opt = { method, headers: { 'Content-Type': 'application/json' } };
    if (body) opt.body = JSON.stringify(body);
    const res = await fetch(`config_api.php?action=${act}`, opt);
    return res.json();
}

async function loadComps() {
    compsCache = await api('list_comps');
    const container = document.getElementById('main-list');
    container.innerHTML = compsCache.map(c => `
        <div class="card">
            <h3 style="margin-top:0">${c.option_label}</h3>
            <p style="font-size:0.85rem; color:var(--text-dim)">Ruta Destino: <strong>${c.next_route_map}</strong></p>
            <div style="display:flex; gap:8px; margin-top:1.5rem">
                <button class="btn btn-outline" style="flex:1" onclick="loadQues('${c.next_route_map}')">Preguntas</button>
                <button class="btn btn-outline" onclick="openCompModal(${c.id_option})">✎</button>
                <button class="btn btn-danger-soft" onclick="del(${c.id_option}, 'comp')">✕</button>
            </div>
        </div>`).join('');
}

async function loadQues(route) {
    currentRoute = route;
    const ques = await api(`list_ques&route=${route}`);
    document.getElementById('m-title').innerText = "Ruta: " + route;
    
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
            <p style="margin:0.5rem 0 0 0; font-size:0.9rem">${q.question_text}</p>
        </div>`).join('');

    document.getElementById('m-body').innerHTML = html || '<p>No hay preguntas.</p>';
    document.getElementById('modal').style.display = 'flex';
}

function openEditQuesModal(codigo, texto, orden) {
    document.getElementById('m-title').innerText = "Editar Pregunta";
    document.getElementById('m-body').innerHTML = `
        <label>Código</label><input type="text" id="q_c" value="${codigo}" readonly style="background:#f8fafc">
        <label>Texto</label><textarea id="q_t" rows="4">${decodeURIComponent(texto)}</textarea>
        <label>Orden</label><input type="number" id="q_o" value="${orden}">
        <button class="btn btn-primary" style="width:100%" onclick="saveQues()">Actualizar</button>
        <button class="btn btn-outline" style="width:100%; margin-top:10px" onclick="loadQues('${currentRoute}')">Cancelar</button>`;
}

async function saveQues() {
    const b = { codigo: document.getElementById('q_c').value, route: currentRoute, texto: document.getElementById('q_t').value, orden: document.getElementById('q_o').value };
    await api('save_ques', 'POST', b);
    loadQues(currentRoute);
}

function openQuesModal() {
    document.getElementById('m-title').innerText = "Nueva Pregunta";
    document.getElementById('m-body').innerHTML = `
        <label>Código</label><input type="text" id="q_c" placeholder="PREG_EJEMPLO">
        <label>Texto</label><textarea id="q_t" rows="3"></textarea>
        <label>Orden</label><input type="number" id="q_o" value="1">
        <button class="btn btn-primary" style="width:100%" onclick="saveQues()">Guardar</button>`;
}

async function del(id, type) {
    if (!confirm("¿Eliminar?")) return;
    await api('delete', 'POST', { id, type });
    type === 'comp' ? loadComps() : loadQues(currentRoute);
}

function openCompModal(id = null) {
    const d = id ? compsCache.find(x => x.id_option == id) : null;
    document.getElementById('m-title').innerText = d ? "Editar Botón" : "Nuevo Botón";
    document.getElementById('m-body').innerHTML = `
        <input type="hidden" id="c_id" value="${id || ''}">
        <label>ID Pregunta Padre</label><input type="number" id="c_p" value="${d ? d.id_question : 1}">
        <label>Texto del Botón</label><input type="text" id="c_l" value="${d ? d.option_label : ''}">
        <label>Ruta Destino</label><input type="text" id="c_r" value="${d ? d.next_route_map : ''}">
        <button class="btn btn-primary" style="width:100%" onclick="saveComp()">Guardar</button>`;
    document.getElementById('modal').style.display = 'flex';
}

async function saveComp() {
    const b = { id: document.getElementById('c_id').value || null, id_parent: document.getElementById('c_p').value, label: document.getElementById('c_l').value, route: document.getElementById('c_r').value };
    await api('save_comp', 'POST', b);
    document.getElementById('modal').style.display = 'none';
    loadComps();
}

window.onload = loadComps;
