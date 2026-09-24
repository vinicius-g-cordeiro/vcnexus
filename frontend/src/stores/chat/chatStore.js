import { defineStore } from "pinia";
import chatService from "@/services/chat/chatService";

export const useChatStore = defineStore("chat", {
  state: () => ({
    usersData: [{}],
    messagesData: [{}],
    conversationData: [{}],
    loading: false,
    error: null,
    hydration: false,
  }),
  actions: {
    async users() {
      const response = await chatService.users();
      this.usersData = response.data;
      return response;
    },
    async messages(uuid) {
      const response = await chatService.messages(uuid);
      this.messagesData = response.data;
      return response;
    },
    async conversation(recipient_id) {
      const response = await chatService.conversations(recipient_id);
      this.conversationData = response.data;
      return response;
    },
  },
});
