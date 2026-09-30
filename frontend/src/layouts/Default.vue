<template>    
    <main>
        <Header />
        <router-view />

        <!-- Webchat on the right bottom side of the screen -->
        <aside class="flex flex-col gap-2">
            <button @click="toggleChat" v-if="!isChatOpen" class="right-4 bottom-4 z-50 fixed flex justify-center items-center bg-zinc-100 dark:bg-zinc-800 shadow-xl rounded-full w-12 h-12">
                <i class="bi-chat-left-text-fill bi"></i>
            </button>
            <Chat v-show="isChatOpen" @click="toggleChat" />
        </aside>
    </main>
</template>

<script setup lang="js">
import { ref } from 'vue'
import Header from '@/components/navigation/Header.vue';
import Chat from '@/views/chat/Chat.vue';

const isChatOpen = ref(false)

const toggleChat = () => {
    isChatOpen.value = !isChatOpen.value
}

</script>
