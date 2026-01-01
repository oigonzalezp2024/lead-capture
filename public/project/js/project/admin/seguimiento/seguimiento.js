import { LeadHTMLAdapter } from "./adapters/LeadHTMLAdapter.js";
import { LeadApiService } from "../../../infrastructure/project/admin/seguimiento/LeadApiService.js";
import { SeguimientoController } from "./SeguimientoController.js";
// --- BOOTSTRAP (Orquestación de Dependencias) ---
const apiService = new LeadApiService();
const htmlAdapter = new LeadHTMLAdapter();
const controller = new SeguimientoController(apiService, htmlAdapter);
// Asignación al ciclo de vida del DOM
window.onload = () => controller.init();
/**
 * EXPOSICIÓN GLOBAL:
 * Necesaria porque el HTML usa 'onclick' y los módulos ES6 tienen scope privado.
 * Esto garantiza que el código funcione igual que el original monolítico.
 */
window.showDetail = (id) => controller.showDetail(id);
window.closeModal = () => controller.closeModal();
//# sourceMappingURL=seguimiento.js.map