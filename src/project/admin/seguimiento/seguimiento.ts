// /project/admin/preguntas/admin.ts

// Estructura de un Lead en el listado general
interface LeadSummary {
    id_prospect: number;
    created_at: string;
    full_name: string;
    profile_type: string;
}

// Estructura de las respuestas detalladas
interface Answer {
    question_key: string;
    question_text?: string;
    answer_value: string;
}

// Estructura completa del detalle de un Lead
interface LeadDetail {
    info: {
        id_prospect: number;
        full_name: string;
        email_whatsapp: string;
        profile_type: string;
        created_at: string;
    };
    answers: Answer[];
}

async function loadLeads(): Promise<void> {
    try {
        const res = await fetch('admin_api.php?action=list');
        if (!res.ok) throw new Error("Error al conectar con la API");

        const data: LeadSummary[] = await res.json();
        const tableBody = document.getElementById('leads-table') as HTMLTableSectionElement | null;

        if (tableBody) {
            tableBody.innerHTML = data.map(p => `
                <tr>
                    <td>#${p.id_prospect}</td>
                    <td>${p.created_at}</td>
                    <td><strong>${escapeHTML(p.full_name)}</strong></td>
                    <td><span class="badge">${p.profile_type}</span></td>
                    <td><button class="view-btn" onclick="showDetail(${p.id_prospect})">Ver Diagnóstico</button></td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error("Fallo al cargar leads:", error);
    }
}

async function showDetail(id: number): Promise<void> {
    try {
        const res = await fetch(`admin_api.php?action=detail&id=${id}`);
        const data: LeadDetail = await res.json();
        
        const body = document.getElementById('modal-body') as HTMLDivElement | null;
        const modal = document.getElementById('modal') as HTMLDivElement | null;

        if (body && modal) {
            let html = `
                <div class="info-section">
                    <h3>Información de Contacto</h3>
                    <p><strong>ID Interno:</strong> #${data.info.id_prospect}</p>
                    <p><strong>Nombre Completo:</strong> ${escapeHTML(data.info.full_name)}</p>
                    <p><strong>Contacto:</strong> ${escapeHTML(data.info.email_whatsapp)}</p>
                    <p><strong>Perfil:</strong> ${data.info.profile_type}</p>
                    <p><strong>Fecha:</strong> ${data.info.created_at}</p>
                </div>
                <h3>Respuestas Detalladas</h3>
            `;

            html += data.answers.map(a => `
                <div class="answer-item">
                    <label>${escapeHTML(a.question_text || 'Código: ' + a.question_key)}</label>
                    <div>${escapeHTML(a.answer_value)}</div>
                </div>
            `).join('');

            body.innerHTML = html;
            modal.style.display = 'flex';
        }
    } catch (error) {
        alert("No se pudo cargar el detalle del prospecto.");
    }
}

// Función auxiliar de seguridad (Anti-XSS)
export function escapeHTML(str: string): string {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function closeModal(): void {
    const modal = document.getElementById('modal');
    if (modal) modal.style.display = 'none';
}

window.onload = () => loadLeads();

// Exponer funciones al HTML
(window as any).showDetail = showDetail;
(window as any).closeModal = closeModal;