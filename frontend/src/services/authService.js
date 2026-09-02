/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import api from '@/services/api'

const authService = {
  async login(credentials) {
    const response = await api.post('/auth/login', credentials)
    return response.data
  },

  async register(userData) {
    const response = await api.post('/auth/register', userData)
    return response.data
  },

  async logout(userData) {
    const response = await api.post('/auth/logout', userData)
    return response.data
  },

  async me() {
    const response = await api.get('/auth/me', { credentials: true })
    return response.data
  },
}

export default authService