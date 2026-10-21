import api from '@/services/apiService'

const tenantService = {
    async tenants() {
        const response = await api.get('/tenants/')
        return response.data
    },
    async fetchTenant(uuid) {
        const response = await api.query(`/tenants/${uuid}`, { withCredentials: true })
        return response.data
    },
    async fetchAllTenants(paginate = false) {
        const response = await api.get('/tenants/', { params: { paginate }, withCredentials: true })
        return response.data
    },
    async createTenant(tenant) {
        const response = await api.post('/tenants/', tenant, { withCredentials: true })
        return response.data
    },
    async deactivate(uuid) {
        const response = await api.delete(`/tenants/deactivate/${uuid}`, { withCredentials: true })
        return response.data
    },
    async activate(uuid) {
        const response = await api.put(`/tenants/activate/${uuid}`, { withCredentials: true })
        return response.data
    }
};

export default tenantService
