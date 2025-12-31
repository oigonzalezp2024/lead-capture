interface OpcionItem {
    lsq_id_question: number;
    lsq_question_text: string;
    lsq_codigo_pregunta: string;
    lsq_route: string;
    lsq_question_type: 'choise' | 'text' | 'textarea';
    lsq_orden: number;
    lsq_visible: number;
    lqo_id_option: number;
    lqo_option_label: string;
    lqo_option_value: string;
    lqo_next_route_map: string;
    lqo_visible: number;
}

interface PreguntaActual {
    id: number;
    codigo: string;
    route: string;
    texto: string;
    tipo: string;
    orden: number;
    visible: number;
}

let idPreguntaParaNuevaOpcion: number = 0;
let datosPreguntaActual: PreguntaActual | null = null;
let listaOpcionesTemporales: { label: string; value: string }[] = [];

// --- UTILIDADES DE SEGURIDAD ---
function escapeHTML(str: string): string {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

async function apiRequest<T>(url: string, data: object): Promise<T | null> {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data as any)
        });
        return await response.json();
    } catch (e) {
        console.error("Error en API:", e);
        return null;
    }
}

// --- LÓGICA DE OPCIONES (DETALLE) ---
function detalleform(base64Data: string): void {
    try {
        // Decodificación fiel al original
        const jsonString = decodeURIComponent(escape(atob(base64Data)));
        const data: OpcionItem[] = JSON.parse(jsonString);
        
        const tablaBody = document.getElementById('cuerpoTablaOpciones') as HTMLTableSectionElement;
        const tituloModal = document.getElementById('txt_pregunta_modal'); 
        
        // Eliminamos o comentamos 'infoPregunta' si no se usa para evitar el error TS6133
        // const infoPregunta = document.getElementById('info_pregunta_detalles'); 
        
        if (!tablaBody) return;
        tablaBody.innerHTML = "";

        if (data.length > 0) {
            idPreguntaParaNuevaOpcion = data[0].lsq_id_question;
            
            if (tituloModal) {
                tituloModal.innerText = "Opciones de: " + data[0].lsq_question_text;
            }

            data.forEach(item => {
                // Replicamos exactamente la generación del Hash para los botones de edición
                let optionHash = btoa(unescape(encodeURIComponent(JSON.stringify(item))));
                let fila = `
                    <tr>
                        <td>${item.lqo_id_option}</td>
                        <td>${item.lqo_option_label}</td>
                        <td>${item.lqo_option_value}</td>
                        <td>${item.lqo_next_route_map}</td>
                        <td class="text-center">${item.lqo_visible == 1 ? 'Sí' : 'No'}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-xs" onclick="prepararEdicion('${optionHash}')">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </button>
                        </td>
                    </tr>`;
                tablaBody.innerHTML += fila;
            });
        }
    } catch (e) { 
        console.error("Error en detalleform", e); 
    }
}

// --- GESTIÓN DE PREGUNTAS ---
async function ejecutarAccionPregunta(accion: string, mensaje: string): Promise<void> {
    const getVal = (id: string) => (document.getElementById(id) as HTMLInputElement)?.value;

    const datos = {
        id_question: getVal('id_questionu'),
        codigo_pregunta: getVal('codigo_preguntau') || getVal('codigo_pregunta'),
        route: getVal('routeu') || getVal('route'),
        question_text: getVal('question_textu') || getVal('question_text'),
        question_type: getVal('question_typeu') || getVal('question_type'),
        orden: getVal('ordenu') || getVal('orden'),
        visible: getVal('visibleu') || getVal('visible')
    };

    const res = await apiRequest<number>(`../modelo/lead_survey_questions_modelo.php?accion=${accion}`, datos);
    if (res == 1) {
        alert(mensaje);
        location.reload();
    }
}

// --- EXPOSICIÓN GLOBAL (Para HTML onclick) ---
(window as any).detalleform = detalleform;
(window as any).prepararEdicion = (hash: string) => {
    const opt = JSON.parse(decodeURIComponent(escape(atob(hash))));
    (document.getElementById('edit_id_option') as HTMLInputElement).value = opt.lqo_id_option;
    (document.getElementById('edit_option_label') as HTMLInputElement).value = opt.lqo_option_label;
    // ... repetir para otros campos de edición ...
    // Aquí disparas el modal manualmente si no usas jQuery:
    const modal = document.getElementById('modalEditarOpcion');
    if (modal) modal.style.display = 'flex';
};
