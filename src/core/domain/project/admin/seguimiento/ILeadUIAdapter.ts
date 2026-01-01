import { LeadSummary } from "./LeadSummary.js";
import { LeadDetail } from "./LeadDetail.js";

/**
 * Interface para la transformación de UI (Adapter Pattern)
 * ISP: Interfaz segregada para evitar dependencias innecesarias.
 */
export interface ILeadUIAdapter {
    /** Genera el HTML para una fila de la tabla */
    renderRow(data: LeadSummary): string;
    /** Genera el HTML para el detalle del Insumo Sagrado */
    renderDetail(data: LeadDetail): string;
}
