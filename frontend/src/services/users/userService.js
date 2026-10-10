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


}

export default userService