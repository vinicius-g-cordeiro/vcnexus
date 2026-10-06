import api from '@/services/apiService'

const authorizationService = {
    async fetchPermissions(){
        const response = await api.get(`/permissions/`)
        return response.data
    },
    async fetchRoles(){
        const response = await api.get(`/roles/`)
        return response.data
    }
}

export default authorizationService;