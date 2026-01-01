var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
export class SeguimientoController {
    constructor(api, ui) {
        this.api = api;
        this.ui = ui;
        this.tableBody = document.getElementById('leads-table');
        this.modal = document.getElementById('modal');
        this.modalBody = document.getElementById('modal-body');
    }
    init() {
        return __awaiter(this, void 0, void 0, function* () {
            try {
                const data = yield this.api.fetchAll();
                if (this.tableBody) {
                    this.tableBody.innerHTML = data.map(l => this.ui.renderRow(l)).join('');
                }
            }
            catch (error) {
                console.error("Initialization error:", error);
            }
        });
    }
    showDetail(id) {
        return __awaiter(this, void 0, void 0, function* () {
            try {
                const data = yield this.api.fetchById(id);
                if (this.modal && this.modalBody) {
                    // El Insumo Sagrado se procesa aquí
                    this.modalBody.innerHTML = this.ui.renderDetail(data);
                    this.modal.style.display = 'flex';
                }
            }
            catch (error) {
                alert("Error al cargar el diagnóstico.");
            }
        });
    }
    closeModal() {
        if (this.modal)
            this.modal.style.display = 'none';
    }
}
//# sourceMappingURL=SeguimientoController.js.map