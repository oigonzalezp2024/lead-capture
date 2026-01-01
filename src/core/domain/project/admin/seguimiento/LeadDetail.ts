// src/core/domain/project/admin/seguimiento/LeadDetail.ts
import { Answer } from "./Answer.js";

export interface LeadDetail {
    readonly info: {
        readonly id_prospect: number;
        readonly full_name: string;
        readonly email_whatsapp: string;
        readonly profile_type: string;
        readonly created_at: string;
    };
    readonly answers: Answer[];
}
