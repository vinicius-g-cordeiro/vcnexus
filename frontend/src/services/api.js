/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import axios from 'axios'
import router from '@/routes/web'

const api = axios.create({
    baseURL: 'http://localhost:80',
    timeout: 10000,
    withCredentials: true,
    headers: {
        'Content-Type' : 'application/json',
        'Accept': 'application/json'
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
