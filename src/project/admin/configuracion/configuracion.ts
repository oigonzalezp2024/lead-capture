// 1. Definición de Contratos (Interfaces)
interface Componente {
    id_option: number;
    id_question: number;
    option_label: string;
    next_route_map: string;
}

interface Pregunta {
    orden: number;
    codigo_pregunta: string;
    question_text: string;
}

// 2. Estado Global Tipado
let compsCache: Componente[] = [];
let currentRoute: string = "";

// ... (manten las interfaces y variables del inicio igual)

// AGREGA ESTAS FUNCIONES QUE FALTABAN O TENÍAN ERRORES DE NOMBRE:

function openQuesModal(): void {
    const title = document.getElementById('m-title');
    const body = document.getElementById('m-body');
    if (title) title.innerText = "Nueva Pregunta";
    if (body) {
        body.innerHTML = `
            <label>Código</label><input type="text" id="q_c" placeholder="PREG_EJEMPLO">
            <label>Texto</label><textarea id="q_t" rows="3"></textarea>
            <label>Orden</label><input type="number" id="q_o" value="1">
            <button class="btn btn-primary" style="width:100%" onclick="saveQues()">Guardar</button>`;
    }
}

function openEditQuesModal(codigo: string, texto: string, orden: number): void {
    const title = document.getElementById('m-title');
    const body = document.getElementById('m-body');
    if (title) title.innerText = "Editar Pregunta";
    if (body) {
        body.innerHTML = `
            <label>Código</label><input type="text" id="q_c" value="${codigo}" readonly style="background:#f8fafc">
            <label>Texto</label><textarea id="q_t" rows="4">${decodeURIComponent(texto)}</textarea>
            <label>Orden</label><input type="number" id="q_o" value="${orden}">
            <button class="btn btn-primary" style="width:100%" onclick="saveQues()">Actualizar</button>
            <button class="btn btn-outline" style="width:100%; margin-top:10px" onclick="loadQues('${currentRoute}')">Cancelar</button>`;
    }
}

async function saveComp(): Promise<void> {
    const idEl = document.getElementById('c_id') as HTMLInputElement;
    const parentEl = document.getElementById('c_p') as HTMLInputElement;
    const labelEl = document.getElementById('c_l') as HTMLInputElement;
    const routeEl = document.getElementById('c_r') as HTMLInputElement;

    const b = { 
        id: idEl.value || null, 
        id_parent: parentEl.value, 
        label: labelEl.value, 
        route: routeEl.value 
    };
    await api('save_comp', 'POST', b);
    const modal = document.getElementById('modal');
    if (modal) modal.style.display = 'none';
    loadComps();
}

async function del(id: any, type: 'comp' | 'ques'): Promise<void> {
    if (!confirm("¿Eliminar?")) return;
    await api('delete', 'POST', { id, type });
    type === 'comp' ? loadComps() : loadQues(currentRoute);
}

// Ahora estas líneas ya no darán error porque las funciones existen arriba:
(window as any).openQuesModal = openQuesModal;
(window as any).openEditQuesModal = openEditQuesModal;
(window as any).saveComp = saveComp;
(window as any).del = del;

// 3. Función API Genérica con Tipado Dinámico
async function api<T>(act: string, method: 'GET' | 'POST' = 'GET', body: object | null = null): Promise<T> {
    const opt: RequestInit = { 
        method, 
        headers: { 'Content-Type': 'application/json' } 
    };
    if (body) opt.body = JSON.stringify(body);
    
    const res = await fetch(`config_api.php?action=${act}`, opt);
    return res.json();
}

// 4. Carga de Componentes (Botones)
async function loadComps(): Promise<void> {
    compsCache = await api<Componente[]>('list_comps');
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
}

// 5. Carga de Preguntas de una Ruta
async function loadQues(route: string): Promise<void> {
    currentRoute = route;
    const ques = await api<Pregunta[]>(`list_ques&route=${route}`);
    
    const title = document.getElementById('m-title');
    const body = document.getElementById('m-body');
    const modal = document.getElementById('modal');

    if (title) title.innerText = "Ruta: " + route;
    
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

    if (body) body.innerHTML = html || '<p>No hay preguntas.</p>';
    if (modal) modal.style.display = 'flex';
}

// 6. Funciones de Guardado con Captura de Elementos Segura
async function saveQues(): Promise<void> {
    const codeEl = document.getElementById('q_c') as HTMLInputElement;
    const textEl = document.getElementById('q_t') as HTMLTextAreaElement;
    const orderEl = document.getElementById('q_o') as HTMLInputElement;

    const b = { 
        codigo: codeEl.value, 
        route: currentRoute, 
        texto: textEl.value, 
        orden: orderEl.value 
    };
    
    await api('save_ques', 'POST', b);
    loadQues(currentRoute);
}

// 7. Utilidad de Seguridad (Anti-XSS)
export function escapeHTML(str: string): string {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function openCompModal(id: number | null = null): void {
    const d = id ? compsCache.find(x => x.id_option == id) : null;
    const title = document.getElementById('m-title');
    const body = document.getElementById('m-body');
    const modal = document.getElementById('modal');

    if (title) title.innerText = d ? "Editar Botón" : "Nuevo Botón";
    
    if (body) {
        body.innerHTML = `
            <input type="hidden" id="c_id" value="${id || ''}">
            <label>ID Pregunta Padre</label><input type="number" id="c_p" value="${d ? d.id_question : 1}">
            <label>Texto del Botón</label><input type="text" id="c_l" value="${d ? d.option_label : ''}">
            <label>Ruta Destino</label><input type="text" id="c_r" value="${d ? d.next_route_map : ''}">
            <button class="btn btn-primary" style="width:100%" onclick="saveComp()">Guardar</button>`;
    }
    
    if (modal) modal.style.display = 'flex';
}

// Globalizar funciones para que el HTML pueda verlas (necesario si usas type="module")
(window as any).loadQues = loadQues;
(window as any).openQuesModal = openQuesModal;
(window as any).openEditQuesModal = openEditQuesModal;
(window as any).saveQues = saveQues;
(window as any).del = del;
(window as any).openCompModal = openCompModal;
(window as any).saveComp = saveComp;

window.onload = () => loadComps();
