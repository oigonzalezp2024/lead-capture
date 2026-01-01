import { ILeadDataService } from "../../../../core/domain/project/admin/seguimiento/ILeadDataService.js";
import { LeadSummary } from "../../../../core/domain/project/admin/seguimiento/LeadSummary.js";
import { LeadDetail } from "../../../../core/domain/project/admin/seguimiento/LeadDetail.js";

export class LeadApiService implements ILeadDataService {
    private readonly baseUrl = 'admin_api.php';

    public async fetchAll(): Promise<LeadSummary[]> {
        const res = await fetch(`${this.baseUrl}?action=list`);
        if (!res.ok) throw new Error("Network error listing leads");
        return res.json();
    }

    public async fetchById(id: number): Promise<LeadDetail> {
        const res = await fetch(`${this.baseUrl}?action=detail&id=${id}`);
        if (!res.ok) throw new Error("Network error fetching lead detail");
        return res.json();
    }
}
