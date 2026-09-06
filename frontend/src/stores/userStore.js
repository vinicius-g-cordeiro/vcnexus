/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import userService from "@/services/userService";

export const useUserStore = defineStore("users", {
  state: () => ({
    users: null,
    user: null,
    loading: false,
    error: null,
  }),

  actions: {
    async search(params) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.search(params);
        this.users = response.data.users;
        return true;
      } catch (error) {
        this.error = error;
        this.users = [];
        return false;
      } finally {
        this.loading = false;
      }
    },
    async fetchUser(params) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.fetchUser(params);
        this.user = response.data.users
        return this.user;
      } catch (error) {
        console.log(error)
        this.error = error;
        this.user = [];
        return this.user;
      } finally {
        this.loading = false;
      }
    },

    async createUser(userInfo) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.create(userInfo);
        this.users = response.data.users;
        return true;
      } catch (error) {
        this.error = error;
        this.users = [];
        return false;
      } finally {
        this.loading = false;
      }
    },
    async updateUser(id, payload) {
      this.loading = true;
      this.error = null;
      try {
        console.log(id,payload)
        const response = await userService.updateUser(id,payload);
        this.user = response.data.user;
        return true;
      } catch (error) {
        this.error = error;
        this.user = [];
        return false;
      } finally {
        this.loading = false;
      }
    },

    async updateUserAvatar(id, formData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.updateUserAvatar(id, formData);
        this.user = response.data.user;
        return true;
      } catch (error) {
        this.error = error;
        this.user = [];
        return false;
      } finally {
        this.loading = false;
      }
    },

    async deleteUser(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.deleteUser(id);
        this.user = response.data.user;
        return true;
      } catch (error) {
        this.error = error;
        this.user = [];
        return false;
      } finally {
        this.loading = false;
      }
    },
    async activateUser(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.activateUser(id);
        this.user = response.data.user;
        return true;
      } catch (error) {
        this.error = error;
        this.user = [];
        return false;
      } finally {
        this.loading = false;
      }
    },
  },
});
