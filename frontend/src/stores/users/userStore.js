/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import userService from "@/services/users/userService";

export const useUserStore = defineStore("users", {
  state: () => ({
    users: null,
    profile: null,
    credentials: null,
    addresses: null,
    contacts: null,
    consents: null,
    sensitive: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchProfile(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.fetchProfile(uuid);
        this.profile = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchCredentials(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.fetchCredentials(uuid);
        this.credentials = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchAddresses(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.fetchAddresses(uuid);
        this.addresses = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchContacts(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.fetchContacts(uuid);
        this.contacts = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
    async fetchPermissions(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.fetchPermissions(uuid);
        this.permissions = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchConsents(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.fetchConsents(uuid);
        this.consents = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchSensitive(uuid) {
      this.loading = true;
      this.error = null;
      try {
        const response = await userService.fetchSensitive(uuid);
        this.sensitive = response.data;
        return true;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
  }
});
