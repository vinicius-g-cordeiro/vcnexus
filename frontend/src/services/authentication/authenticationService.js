import api from '@/services/apiService'

const authenticationService = {
    async login(loginRequest) {
        const response = await api.post('/auth/login', loginRequest)
        return response.data
    },
    async logout(logoutRequest) {
        const response = await api.post('/auth/logout', logoutRequest)
        return response.data
    },
    async getAuthenticatedUser() {
        const response = await api.get('/auth/me', { withCredentials: true })
        return response.data
    }
}

export default authenticationService