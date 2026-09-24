import { defineStore } from "pinia";
import authenticationService from "@/services/authentication/authenticationService";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    sessionUser: null,
    loading: false,
    error: null,
    hydration: false,
  }),
 
  getters: {
    isAuthenticated: (state) => !!state.sessionUser,
    isSuperAdmin: (state) => !!state.sessionUser && state.sessionUser?.roles.indexOf('1') >= 0,
    isWorker: (state) =>  !!state.sessionUser && state.sessionUser?.roles.indexOf('3') >= 0,
    canManageAccess: (state) =>  !!state.sessionUser && (state.sessionUser.roles.indexOf('2') >= 0 || state.sessionUser?.roles.indexOf('1') >= 0),
    userPermissions: (state) =>  state?.sessionUser?.permissions,
    isAdmin: (state) => !!state.sessionUser && state.sessionUser?.roles.indexOf('2') >= 0
  },
  actions: {
    /**
     *
     * @param {Array} permission
     * @returns bool
     */
    hasPermissions(permission = Array.prototype()) {
      const permissions = permission.map(
        (item) => !!this.sessionUser.permissions.find((p) => p === item),
      );
      return permissions.some((item) => item === true);
    },
    async login(credentials) {
      try {
        this.loading = true;
        this.error = null;
        const response = await authenticationService.login(credentials);
        this.sessionUser = response.data;
        this.hydration = true;
        return true;
      } catch (e) {
        this.error = e.response?.data?.message || "Erro ao fazer login";
        return false;
      } finally {
        this.loading = false;
      }
    },
    async logout() {
      try {
        this.loading = true;
        this.error = null;
        const obj = { uuid: this.sessionUser?.uuid };
        const response = await authenticationService.logout(obj);
        this.sessionUser = null;
        this.hydration = true; // still "hydrated" — we know: nobody's logged in
        return true;
      } catch (e) {
        this.error = e?.response?.data?.message || "Erro ao fazer logout";
        return false;
      } finally {
        this.loading = false;
      }
    },
    async getAuthenticatedUser() {
      // Don't refetch if we already know the user for this session
      if (this.hydration && this.sessionUser) return true;

      this.loading = true;
      try {
        const response = await authenticationService.getAuthenticatedUser();
        this.sessionUser = response.data;
        return true;
      } catch (e) {
        this.sessionUser = null;
        this.error = e?.response?.data?.message || "Erro ao carregar usuário";
        return false;
      } finally {
        this.loading = false;
        this.hydration = true;
      }
    },
  },
});
