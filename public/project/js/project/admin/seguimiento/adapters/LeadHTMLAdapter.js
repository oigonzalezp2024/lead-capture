import { DomSanitizer } from "../../../../infrastructure/project/admin/seguimiento/DomSanitizer.js";
export class LeadHTMLAdapter {
    renderRow(p) {
        return `
            <tr>
                <td>#${p.id_prospect}</td>
                <td>${p.created_at}</td>
                <td><strong>${DomSanitizer.escape(p.full_name)}</strong></td>
                <td><span class="badge">${p.profile_type}</span></td>
                <td><button class="view-btn" onclick="showDetail(${p.id_prospect})">Ver Diagnóstico</button></td>
            </tr>
        `;
    }
    renderDetail(data) {
        let html = `
            <div class="info-section">
                <h3>Información de Contacto</h3>
                <p><strong>ID Interno:</strong> #${data.info.id_prospect}</p>
                <p><strong>Nombre Completo:</strong> ${DomSanitizer.escape(data.info.full_name)}</p>
                <p><strong>Contacto:</strong> ${DomSanitizer.escape(data.info.email_whatsapp)}</p>
                <p><strong>Perfil:</strong> ${data.info.profile_type}</p>
                <p><strong>Fecha:</strong> ${data.info.created_at}</p>
            </div>
            <h3>Respuestas Detalladas</h3>
        `;
        html += data.answers.map(a => `
            <div class="answer-item">
                <label>${DomSanitizer.escape(a.question_text || 'Código: ' + a.question_key)}</label>
                <div>${DomSanitizer.escape(a.answer_value)}</div>
            </div>
        `).join('');
        return html;
    }
}
//# sourceMappingURL=LeadHTMLAdapter.js.map