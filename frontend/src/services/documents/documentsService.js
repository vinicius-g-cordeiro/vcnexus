import api from '@/services/apiService'

const documentsService = {
    async fetchAllDocuments(params){
        const response = await api.query(`/documents/`, params)
        return response.data
    },
    async createDocument(form){
      const response = await api.post('/documents/', form)
      return response.data
    }
}

export default documentsService;
