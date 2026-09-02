/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import authService from "@/services/authService";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    sessionUser: null,
    loading: false,
    error: null,
    hydration: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.sessionUser,
  },

  actions: {
    async fetchUser() {
      // Don't refetch if we already know the user for this session
      if (this.hydration && this.sessionUser) return true;

      this.loading = true;
      try {
        const response = await authService.me();
        this.sessionUser = response.data.user;
        return true;
      } catch (e) {
        this.sessionUser = null;
        return false;
      } finally {
        this.loading = false;
        this.hydration = true;
      }
    },

    async login(credentials) {
      try {
        this.loading = true;
        this.error = null;
        const response = await authService.login(credentials);
        this.sessionUser = response.data.user;
        console.log(this.sessionUser);
        this.hydration = true; // we now know who's logged in, no need to refetch
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
        const obj = { uuid: this.sessionUser.uuid };
        const response = await authService.logout(obj);
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

    async register(userData) {
      try {
        this.loading = true;
        this.error = null;
        const response = await authService.register(userData);
        return true;
      } catch (e) {
        this.error = e.response.data.message || "Erro ao registrar";
        return false;
      } finally {
        this.loading = false;
      }
    },

    // add this so you can force a re-check when you actually want one
    async refreshUser() {
      this.hydration = false;
      return this.fetchUser();
    },

    async updateUser(userData) {
      try {
        this.loading = true;
        const response = await authService.edit(userData);
        this.sessionUser = response.data.user;
        return true;
      } catch (e) {
        this.error = e.response.data.message || "Erro ao atualizar perfil";
        return false;
      } finally {
        this.loading = false;
        this.hydration = true;
      }
    },
  },
});
