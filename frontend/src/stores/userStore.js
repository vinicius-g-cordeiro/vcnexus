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
    loading: false,
    error: null,
  }),

  actions: {
    async search(params) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.search(params);
        this.users = response.data.users
        return true;
      } catch (error) {
        this.error = error;
        this.users = [];
        return false;
      } finally {
        this.loading = false;
      }
    },
  },
});
