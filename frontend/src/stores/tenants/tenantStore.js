/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import tenantsService from "@/services/tenants/tenantsService";

export const useTenantStore = defineStore("users", {
  state: () => ({
    tenant: null,
    tenants: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchTenants() {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantsService.fetchTenants();
        this.tenant = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async createTenant(form) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantsService.createTenant(form);
        return response.data;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async deactivate(form) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantsService.deactivate(form);
        return response.data;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async activate(form) {
      this.loading = true;
      this.error = null;
      try {
        const response = await tenantsService.activate(form);
        return response.data;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
  }
});
