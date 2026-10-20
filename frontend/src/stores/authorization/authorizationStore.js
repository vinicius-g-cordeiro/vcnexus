/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import authorizationService from "@/services/authorization/authorizationService";

export const useAuthorizationStore = defineStore("authorization", {
  state: () => ({
    loading: false,
    loadingMessage: "Loading...",
    error: null,
    message: null,
    permissions: null,
    roles: null,
    user_permissions: null,
    user_roles: null
  }),
  actions: {
    async fetchPermissions() {
      this.loading = true;
      this.error = null;
      try {
        const response = await authorizationService.fetchPermissions();
        this.permissions = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchRoles() {
      this.loading = true;
      this.error = null;
      try {
        const response = await authorizationService.fetchRoles();
        this.roles = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchUserPermissions(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await authorizationService.fetchUserPermissions(uuid);
        this.user_permissions = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchUserRoles(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await authorizationService.fetchUserRoles(uuid);
        this.user_roles = response.data.list;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    }
  },
});
