<script setup lang="js">
import { ref, onMounted } from 'vue'
import Header from '@/components/navigation/Header.vue'
import Chat from '@/views/chat/Chat.vue'
import Sidebar from '@/components/navigation/Sidebar.vue'
import { useAuthStore } from '@/stores/authentication/authenticationStore'

const authStore = useAuthStore()
const isChatOpen = ref(false)

function toggleChat() {
    isChatOpen.value = !isChatOpen.value
    
}

onMounted(async () => {
    await authStore.getMenus()
})
</script>

<template>
    <main class="flex min-h-screen">
        <Sidebar app-name="VCNexus" :items="authStore.menus" />

        <div class="flex flex-col flex-1 min-w-0">
            <Header />
            <router-view class="mx-auto mt-12 p-2 w-11/12" />
        </div>

        <aside class="flex flex-col gap-2">
            <button v-if="!isChatOpen" class="right-4 bottom-4 z-50 fixed flex justify-center items-center bg-zinc-100 dark:bg-zinc-800 shadow-xl rounded-full w-12 h-12" @click="toggleChat">
                <i class="bi-chat-left-text-fill bi"></i>
            </button>
            <Chat v-if="isChatOpen" @click="toggleChat"  />
        </aside>
    </main>
</template>