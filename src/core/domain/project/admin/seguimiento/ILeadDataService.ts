// src/core/domain/project/admin/seguimiento/ILeadDataService.ts
import { LeadSummary } from "./LeadSummary.js";
import { LeadDetail } from "./LeadDetail.js";

export interface ILeadDataService {
    /** Obtiene el listado resumido para la tabla principal */
    fetchAll(): Promise<LeadSummary[]>;
    
    /** Obtiene el detalle completo del Insumo Sagrado por ID */
    fetchById(id: number): Promise<LeadDetail>;
}