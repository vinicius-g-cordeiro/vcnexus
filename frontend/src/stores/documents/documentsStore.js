/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { defineStore } from "pinia";
import documentsService from "@/services/documents/documentsService";

export const useDocumentsStore = defineStore("documents", {
  state: () => ({
    loading: false,
    loadingMessage: "Loading...",
    error: null,
    message: null,
    documents: [],
    document: null
  }),
  actions: {
    async fetchAllDocuments(params) {
      this.loading = true
      this.error = null
      try{
        const response = await documentsService.fetchAllDocuments(params);
        this.documents = response.data.list;
        return true;
      }catch(error){
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    }
  },
});
