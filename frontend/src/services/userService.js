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

  async updateUser(id, payload) {
    const response = await api.put(`/users/${id}`, payload);
    return response.data;
  },

  async updateUserAvatar(id, formData) {
    const response = await api.post(`/users/${id}/avatar`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async deleteUser(id) {
    const response = await api.delete(`/users/delete/${id}`);
    return response.data;
  },


  async activateUser(id) {
    const response = await api.put(`/users/activate/${id}`);
    return response.data;
  },
};

export default userService;
