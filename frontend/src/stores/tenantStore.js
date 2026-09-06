/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import tenantService from "@/services/tenantService";

export const useTenantStore = defineStore("tenants", {
  state: () => ({
    tenants: null,
    loading: false,
    error: null,
  }),

  actions: {
    async search(params) {
      this.loading = true;
      this.error = null;

      try {
        const response = await tenantService.search(params);
        this.tenants = response.data.tenants;
        return true;
      } catch (error) {
        this.error = error;
        this.tenants = [];
        return false;
      } finally {
        this.loading = false;
      }
    },

    async register(tenantInfo) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantService.register(tenantInfo);
        return true;
      } catch (e) {
        this.error = e.response.data.message || "Erro ao registrar";
        return false;
      } finally {
        this.loading = false;
      }
    },
  },
});
