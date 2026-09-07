/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import api from "@/services/api";

const userService = {
  async search(params) {
    const response = await api.get("/users/list", {
      params,
    });

    return response.data;
  },

  async fetchUser(params) {
    const response = await api.get(`/users/${params}`);
    return response.data;
  },

  async create(userData) {
    const response = await api.post("/users/create/", userData);
    return response.data;
  },

  async updateUser(uuid, payload) {
    const response = await api.put(`/users/${uuid}`, payload);
    return response.data;
  },

  async updateUserAvatar(uuid, formData) {
    const response = await api.post(`/users/${uuid}/avatar`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async deactivateUser(uuid) {
    const response = await api.delete(`/users/deactivate/${uuid}`);
    return response.data;
  },


  async activateUser(uuid) {
    const response = await api.put(`/users/activate/${uuid}`);
    return response.data;
  },

  async blockUser(uuid) {
    const response = await api.put(`/users/block/${uuid}`);
    return response.data;
  },


  async unblockUser(uuid) {
    const response = await api.put(`/users/unblock/${uuid}`);
    return response.data;
  },
};

export default userService;
