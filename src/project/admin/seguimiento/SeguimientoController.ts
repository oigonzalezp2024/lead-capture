import { ILeadDataService } from "../../../core/domain/project/admin/seguimiento/ILeadDataService.js";
import { ILeadUIAdapter } from "../../../core/domain/project/admin/seguimiento/ILeadUIAdapter.js";

export class SeguimientoController {
    private readonly tableBody = document.getElementById('leads-table') as HTMLTableSectionElement | null;
    private readonly modal = document.getElementById('modal') as HTMLDivElement | null;
    private readonly modalBody = document.getElementById('modal-body') as HTMLDivElement | null;

    constructor(
        private api: ILeadDataService, 
        private ui: ILeadUIAdapter
    ) {}

    public async init(): Promise<void> {
        try {
            const data = await this.api.fetchAll();
            if (this.tableBody) {
                this.tableBody.innerHTML = data.map(l => this.ui.renderRow(l)).join('');
            }
        } catch (error) {
            console.error("Initialization error:", error);
        }
    }

    public async showDetail(id: number): Promise<void> {
        try {
            const data = await this.api.fetchById(id);
            if (this.modal && this.modalBody) {
                // El Insumo Sagrado se procesa aquí
                this.modalBody.innerHTML = this.ui.renderDetail(data);
                this.modal.style.display = 'flex';
            }
        } catch (error) {
            alert("Error al cargar el diagnóstico.");
        }
    }

    public closeModal(): void {
        if (this.modal) this.modal.style.display = 'none';
    }
}
