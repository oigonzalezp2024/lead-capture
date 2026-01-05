interface Insumo {
    id?: number;
    nombre: string;
    descripcion?: string;
    cantidad: number;
    unidadMedida: string;
    ubicacion: string;
    fechaUltimaActualizacion?: string; // ISO date string
}

class InsumoService {
    private baseUrl: string;

    constructor(baseUrl: string) {
        this.baseUrl = baseUrl;
    }

    private async request<T>(method: string, endpoint: string, data?: any): Promise<T> {
        const url = `${this.baseUrl}${endpoint}`;
        const options: RequestInit = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: data ? JSON.stringify(data) : undefined,
        };

        try {
            const response = await fetch(url, options);

            if (!response.ok) {
                let errorDetails: any = { message: response.statusText };
                try {
                    errorDetails = await response.json();
                } catch (e) {
                    // If response is not JSON, use status text
                }
                throw new Error(`API Error ${response.status}: ${errorDetails.message || JSON.stringify(errorDetails)}`);
            }

            // Handle 204 No Content for DELETE or other operations that don't return a body
            if (response.status === 204 || response.headers.get('content-length') === '0') {
                return null as T;
            }

            return await response.json() as T;
        } catch (error) {
            console.error(`Error during ${method} request to ${url}:`, error);
            throw error;
        }
    }

    async getAllInsumos(): Promise<Insumo[]> {
        return this.request<Insumo[]>('GET', '/insumos');
    }

    async getInsumoById(id: number): Promise<Insumo> {
        return this.request<Insumo>('GET', `/insumos/${id}`);
    }

    async createInsumo(insumo: Omit<Insumo, 'id' | 'fechaUltimaActualizacion'>): Promise<Insumo> {
        return this.request<Insumo>('POST', '/insumos', insumo);
    }

    async updateInsumo(id: number, insumo: Partial<Omit<Insumo, 'id' | 'fechaUltimaActualizacion'>>): Promise<Insumo> {
        return this.request<Insumo>('PUT', `/insumos/${id}`, insumo);
    }

    async deleteInsumo(id: number): Promise<void> {
        await this.request<void>('DELETE', `/insumos/${id}`);
    }
}