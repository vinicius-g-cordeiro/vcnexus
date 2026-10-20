import api from '@/services/apiService'

const chatService = {
    async users() {
        const response = await api.get('/chat/users/')
        return response.data
    },
    async messages(roomId, { limit = 50, before = null } = {}) {
        const params = { limit }
        if (before !== null) params.before = before
        const response = await api.get(`/chat/rooms/${roomId}/messages/`, { params })
        return response.data
    },
    async conversations(recipient_id) {
        const response = await api.post(`/chat/conversations/`, { recipient_id: recipient_id })
        return response.data
    },
    async wsTicket() {
        const response = await api.post('/chat/ws-ticket/')
        return response.data
    },
    async sendAudioMessage(form) {
        const response = await api.post('/chat/room/audio-message/', form)
        return response.data
    }
}

export default chatService