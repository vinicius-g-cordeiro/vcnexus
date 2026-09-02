/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import api from '@/services/api'

const userService = {
  
    async search(params) {
        const response = await api.get('/users/list', {
            params
        })

        return response.data
    }

}

export default userService