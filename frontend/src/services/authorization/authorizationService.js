import api from '@/services/apiService'

const authorizationService = {
    async fetchPermissions(){
        const response = await api.get(`/permissions/`)
        return response.data
    },
    async fetchRoles(){
        const response = await api.get(`/roles/`)
        return response.data
    },
    async fetchUserPermissions(uuid){
        const response = await api.query(`/permissions/${uuid}/`)
        return response.data
    },
    async fetchUserRoles(uuid){
        const response = await api.query(`/roles/${uuid}/`)
        return response.data
    }
}

export default authorizationService;