import api from '@/services/apiService'

const userService = {
    async fetchProfile(uuid) {
        const response = await api.query(`/users/profile/${uuid}`, { withCredentials: true })
        return response.data
    },
    async fetchCredentials(uuid) {
        const response = await api.query(`/users/credentials/${uuid}`, { withCredentials: true })
        return response.data
    },

    async fetchAddresses(uuid) {
        const response = await api.query(`/users/addresses/${uuid}`, { withCredentials: true })
        return response.data
    },

    async fetchContacts(uuid) {
        const response = await api.query(`/users/contacts/${uuid}`, { withCredentials: true })
        return response.data
    },

    async fetchConsents(uuid) {
        const response = await api.query(`/users/consents/${uuid}`, { withCredentials: true })
        return response.data
    },

    async fetchSensitive(uuid) {
        const response = await api.query(`/users/sensitive/${uuid}`, { withCredentials: true })
        return response.data
    },
    async createUser(form) {
        const response = await api.post('/users/', form)
        return response.data
    },
    async fetchEducation(uuid) {
        const response = await api.query(`/users/education/${uuid}`, { withCredentials: true })
        return response.data
    },
    async deactivate(uuid) {
        const response = await api.delete(`/users/deactivate/${uuid}`, { withCredentials: true })
        return response.data
    },
    async activate(uuid) {
        const response = await api.put(`/users/activate/${uuid}`, { withCredentials: true })
        return response.data
    },
    async block(uuid){
        const response = await api.put(`/users/block/${uuid}`, { withCredentials: true })
        return response.data
    },
    async unblock(uuid){
        const response = await api.put(`/users/unblock/${uuid}`, { withCredentials: true })
        return response.data
    }

}

export default userService