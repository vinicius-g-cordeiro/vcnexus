<script setup>
import {
    computed,
    defineEmits,
    onBeforeUnmount,
    onMounted,
    ref,
} from 'vue'

import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { useChatStore } from '@/stores/chat/chatStore'

const emit = defineEmits(['click'])

const authStore = useAuthStore()
const chatStore = useChatStore();

const socket = ref(null)

const status = ref('Disconnected')
const connected = ref(false)

const message = ref('')
const messages = ref([])

const users = ref([])
const selectedChat = ref(null)

const currentRoomId = ref(null)
const loadingConversation = ref(false)
const sendingMessage = ref(false)

const currentRoom = computed(() => {
    return currentRoomId.value
})

/*
|--------------------------------------------------------------------------
| WebSocket
|--------------------------------------------------------------------------
*/

function connect() {
    if (
        socket.value &&
        (
            socket.value.readyState === WebSocket.OPEN ||
            socket.value.readyState === WebSocket.CONNECTING
        )
    ) {
        return
    }

    status.value = 'Connecting...'

    socket.value = new WebSocket('ws://localhost:8080')

    socket.value.onopen = () => {
        status.value = 'Connected'
        connected.value = true

        console.log('WebSocket connected')
    }

    socket.value.onmessage = handleSocketMessage

    socket.value.onerror = (error) => {
        console.error('WebSocket error:', error)

        status.value = 'Error'
        connected.value = false
    }

    socket.value.onclose = () => {
        status.value = 'Disconnected'
        connected.value = false

        currentRoomId.value = null

        console.log('WebSocket disconnected')
    }
}

function handleSocketMessage(event) {
    let payload

    try {
        payload = JSON.parse(event.data)
    } catch (error) {
        console.error('Invalid WebSocket payload:', event.data)

        return
    }

    switch (payload.event) {
        case 'connected':
            console.log(
                'WebSocket connection:',
                payload.data?.connection_id
            )
            break

        case 'room_joined':
            console.log(
                'Joined room:',
                payload.data?.room_id
            )
            break

        case 'message':
            handleIncomingMessage(payload.data)
            break

        case 'error':
            console.error(
                'WebSocket error:',
                payload.data?.message
            )
            break

        default:
            console.warn(
                'Unknown WebSocket event:',
                payload.event
            )
    }
}

function handleIncomingMessage(data) {
    if (!data?.room_id) {
        return
    }

    /*
     * Don't display messages belonging to another conversation.
     */
    if (Number(data.room_id) !== Number(currentRoomId.value)) {
        return
    }

    messages.value.push({
        id: data.id ?? null,
        roomId: data.room_id,
        userId: data.user_id ?? data.from,
        content: data.message,
        createdAt: data.created_at ?? new Date().toISOString(),
    })
}

/*
|--------------------------------------------------------------------------
| Rooms
|--------------------------------------------------------------------------
*/

function joinRoom(roomId) {
    if (
        !connected.value ||
        !socket.value ||
        socket.value.readyState !== WebSocket.OPEN
    ) {
        return
    }

    socket.value.send(JSON.stringify({
        action: 'join_room',
        room_id: roomId,
    }))
}

function leaveRoom(roomId) {
    if (
        !connected.value ||
        !socket.value ||
        socket.value.readyState !== WebSocket.OPEN
    ) {
        return
    }

    socket.value.send(JSON.stringify({
        action: 'leave_room',
        room_id: roomId,
    }))
}

/*
|--------------------------------------------------------------------------
| Conversations
|--------------------------------------------------------------------------
*/

/**
 * Open a conversation with another user.
 */
async function changeConversation(user) {
    if (!user?.id) {
        return
    }

    if (selectedChat.value?.id === user.id) {
        return
    }

    loadingConversation.value = true

    try {
        /*
         * Leave the currently selected WebSocket room.
         */
        if (currentRoomId.value !== null) {
            leaveRoom(currentRoomId.value)
        }

        selectedChat.value = user

        currentRoomId.value = null

        messages.value = []

        /*
         * Ask the backend for the conversation.
         *
         * The backend should:
         *
         * 1. Check whether a room already exists between
         *    the authenticated user and `user.id`.
         *
         * 2. Create it if it doesn't exist.
         *
         * 3. Return the room.
         */

        const ok = await chatStore.conversation(user.id)
        if (!ok) {
            throw new Error(
                'Unable to open conversation.'
            )
        }

        const conversation = chatStore.conversationData

        /*
         * Example expected response:
         *
         * {
         *     "id": 123
         * }
         */
        currentRoomId.value = conversation.id

        /*
         * Load persisted messages from PostgreSQL.
         */
        await loadMessages(currentRoomId.value)

        /*
         * Join the corresponding WebSocket room.
         */
        joinRoom(currentRoomId.value)

    } catch (error) {
        console.error(
            'Unable to open conversation:',
            error
        )

        currentRoomId.value = null
        messages.value = []

    } finally {
        loadingConversation.value = false
    }
}

/*
|--------------------------------------------------------------------------
| Message history
|--------------------------------------------------------------------------
*/

async function loadMessages(roomId) {
    const ok = await chatStore.messages(roomId)
    if (!ok) {
        throw new Error(
            'Unable to load messages.'
        )
    }

    const data = chatStore.messagesData

    /*
     * Adapt this depending on your API response.
     */
    messages.value = data.map(message => ({
        id: message.id,
        roomId: message.room_id,
        userId: message.user_id,
        content: message.content,
        createdAt: message.created_at,
    }))
}

/*
|--------------------------------------------------------------------------
| Sending messages
|--------------------------------------------------------------------------
*/

function sendMessage() {
    if (
        !connected.value ||
        !currentRoomId.value ||
        !message.value.trim() ||
        sendingMessage.value
    ) {
        return
    }

    if (
        !socket.value ||
        socket.value.readyState !== WebSocket.OPEN
    ) {
        return
    }

    sendingMessage.value = true

    socket.value.send(
        JSON.stringify({
            action: 'send_message',
            room_id: currentRoomId.value,
            message: message.value.trim(),
        })
    )

    message.value = ''

    sendingMessage.value = false
}

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/

async function loadUsers() {
    try {

        const ok = await chatStore.users();
        if (!ok) {
            throw new Error(
                'Unable to load chat users.'
            )
        }

        const data = chatStore.usersData

        /*
         * Don't show yourself in the list.
         */
        users.value = data.filter(
            user =>
                Number(user.id) !==
                Number(authStore.sessionUser.id)
        )

    } catch (error) {
        console.error(
            'Unable to load chat users:',
            error
        )
    }
}

/*
|--------------------------------------------------------------------------
| Lifecycle
|--------------------------------------------------------------------------
*/

onMounted(async () => {
    connect()

    await loadUsers()
})

onBeforeUnmount(() => {
    if (currentRoomId.value !== null) {
        leaveRoom(currentRoomId.value)
    }

    socket.value?.close()
})

/*
|--------------------------------------------------------------------------
| UI
|--------------------------------------------------------------------------
*/

const toggleChat = (event) => {
    emit('click', event)
}
</script>

<template>
    <main class="right-6 bottom-6 fixed flex flex-col gap-2 bg-stone-100 dark:bg-stone-800 shadow-xl p-2 border-stone-200 w-4/12">
        <!-- Header -->

        <section class="flex flex-row justify-between items-center">
            <h1 class="font-bold text-4xl sm:text-5xl tracking-tight">
                Chat
            </h1>

            <button type="button" @click="toggleChat" class="bg-stone-100 hover:bg-stone-300 dark:bg-stone-800 dark:hover:bg-stone-700">
                <i class="mx-auto p-2 text-2xl bi bi-dash-lg"></i>
            </button>
        </section>

        <!-- Chat -->

        <section class="flex flex-row justify-between gap-2">
            <!-- Users -->

            <aside class="flex flex-col bg-stone-100 dark:bg-stone-700 w-1/3">
                <div v-if="users.length === 0" class="p-3 text-stone-500 text-sm">
                    No users available.
                </div>

                <button v-for="user in users" :key="user.id" type="button" @click="changeConversation(user)" class="flex flex-row justify-between items-center hover:bg-stone-200 dark:hover:bg-stone-600 p-2 border-stone-200 border-b text-left">
                    <span class="self-center font-semibold dark:text-white text-sm">
                        {{ user.name }}
                    </span>

                    <span>
                        <i v-if="user.isOnline" class="text-green-500 bi bi-circle-fill"></i>

                        <i v-else class="text-stone-500 bi bi-circle-fill"></i>
                    </span>
                </button>
            </aside>

            <!-- Conversation -->

            <div class="flex flex-col gap-2 w-2/3">
                <p v-if="selectedChat" class="flex self-center gap-2 font-semibold dark:text-white text-sm whitespace-nowrap">
                    Talking to:

                    <strong>
                        {{ selectedChat.name }}
                    </strong>

                    <i v-if="status === 'Connected'" class="text-green-500 bi bi-circle-fill"></i>

                    <i v-else class="text-stone-500 bi bi-circle-fill"></i>
                </p>

                <p v-else class="self-center text-stone-500 text-sm">
                    Select a user.
                </p>

                <!-- Messages -->

                <div class="flex flex-col gap-2">
                    <div class="messages">
                        <div v-if="loadingConversation" class="text-stone-500 text-sm">
                            Loading conversation...
                        </div>

                        <div v-else-if="messages.length === 0 && selectedChat" class="text-stone-500 text-sm">
                            No messages yet.
                        </div>

                        <div v-for="item in messages" :key="item.id ?? `${item.createdAt}-${item.content}`" class="message">
                            <strong>
                                {{ item.userId }}:
                            </strong>

                            {{ item.content }}
                        </div>
                    </div>

                    <!-- Input -->

                    <form v-if="selectedChat" @submit.prevent="sendMessage" class="flex gap-2">
                        <input v-model="message" type="text" placeholder="Type a message..." :disabled="!connected ||
                            !currentRoomId ||
                            loadingConversation
                            " />

                        <button type="submit" :disabled="!connected ||
                            !currentRoomId ||
                            !message.trim() ||
                            sendingMessage
                            ">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Status -->

        <small class="text-stone-500">
            {{ status }}
        </small>
    </main>
</template>

<style scoped>
.messages {
    min-height: 300px;
    max-height: 400px;
    overflow-y: auto;
    padding: 16px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.message {
    margin-bottom: 8px;
}

input {
    flex: 1;
    padding: 8px;
}

button {
    cursor: pointer;
}
</style>