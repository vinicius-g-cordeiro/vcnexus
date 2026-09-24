import api from '@/services/apiService'

const authenticationService = {
    async users() {
        const response = await api.get('/chat/users/')
        return response.data
    },
    async messages(uuid) {
        const response = await api.get(`/chat/rooms/${uuid}/messages/`)
        return response.data
    },
    async conversations(recipient_id) {
        const response = await api.post(`/chat/conversations/`, { recipient_id: recipient_id } )
        return response.data
    }
}

export default authenticationService