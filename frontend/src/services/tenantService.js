/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import api from "@/services/api";

const tenantService = {
  async search(params) {
    const response = await api.get("/tenants/list", {
      params,
    });
    return response.data;
  },
  async register(tenantInfo) {
    const response = await api.post("/tenants/save", tenantInfo);
    return response.data;
  },

  async fetchTenant(params) {
    const response = await api.get(`/tenants/${params}`);
    return response.data;
  },
};

export default tenantService;
