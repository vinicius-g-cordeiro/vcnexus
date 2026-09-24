import axios from 'axios'
import router from '@/router/web'

const api = axios.create({
    baseURL: 'http://localhost:80/v1/',
    timeout: 10000,
    withCredentials: true,
    headers: {
        'Content-Type' : 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        "X-Idempotency-Key": Math.random().toString(36).substring(2, 15)
    }
})

api.interceptors.response.use(
    response => response,
    error => {
        if(error.response?.status === 401){
            router.push({name: 'login'})
        }

        return Promise.reject(error)
    }
)

export default api;
