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

const WS_URL = import.meta.env.VITE_WS_URL ?? 'ws://localhost:8080'

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
const chatContainerRef = ref(null)
const recording = ref(false)

const isRecording = computed(() => {
    return recording.value
})

// get microfone access
const isMicrophoneAccessible = ref(false)

isMicrophoneAccessible.value = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)


const isChatOpen = ref(false)

const currentRoom = computed(() => {
    return currentRoomId.value
})

function startRecording() {
    recording.value = true

    navigator.mediaDevices.getUserMedia({ audio: true })
        .then((stream) => {
            const mediaRecorder = new MediaRecorder(stream)
            const audioChunks = []

            mediaRecorder.addEventListener('dataavailable', (event) => {
                audioChunks.push(event.data)
            })

            mediaRecorder.addEventListener('stop', () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' })
                const audioUrl = URL.createObjectURL(audioBlob)
                const audio = new Audio(audioUrl)
                audio.play()
            })

            mediaRecorder.start()    
        })
        .catch((error) => {
            console.error('Error accessing microphone:', error)
        })
        .finally(() => {
            recording.value = false
            
    })
}

/*
 * Reconnection state.
 */
let unmounted = false
let reconnectAttempts = 0
let reconnectTimer = null

/*
|--------------------------------------------------------------------------
| WebSocket
|--------------------------------------------------------------------------
*/

async function connect() {
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

    /*
     * The server identifies the user through a short-lived, single-use
     * ticket issued by the HTTP API (session cookie), not through ids
     * sent by the browser.
     */
    let ticket

    try {
        ticket = await chatStore.wsTicket()
    } catch (error) {
        console.error('Unable to get WebSocket ticket:', error)

        status.value = 'Error'
        connected.value = false

        scheduleReconnect()

        return
    }

    if (unmounted) {
        return
    }

    const ws = new WebSocket(`${WS_URL}?ticket=${encodeURIComponent(ticket)}`)

    socket.value = ws

    ws.onopen = () => {
        status.value = 'Connected'
        connected.value = true
        reconnectAttempts = 0

        console.log('WebSocket connected')
    }

    ws.onmessage = handleSocketMessage

    ws.onerror = (error) => {
        console.error('WebSocket error:', error)

        status.value = 'Error'
        connected.value = false
    }

    ws.onclose = () => {
        status.value = 'Disconnected'
        connected.value = false

        console.log('WebSocket disconnected')

        /*
         * currentRoomId is kept on purpose: once reconnected we
         * rejoin the same room (see the `connected` event below).
         */
        if (!unmounted) {
            scheduleReconnect()
        }
    }
}

function scheduleReconnect() {
    if (unmounted || reconnectTimer !== null) {
        return
    }

    /*
     * 1s, 2s, 4s ... capped at 15s.
     */
    const delay = Math.min(1000 * 2 ** reconnectAttempts, 15000)

    reconnectAttempts++

    reconnectTimer = setTimeout(() => {
        reconnectTimer = null

        connect()
    }, delay)
}

async function resumeRoom() {
    /*
     * After a reconnect: rejoin the room and pull anything we missed.
     */
    if (currentRoomId.value === null) {
        return
    }

    const roomId = currentRoomId.value

    joinRoom(roomId)

    try {
        await loadMessages(roomId)
    } catch (error) {
        console.error('Unable to refresh messages:', error)
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

            resumeRoom()
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

    /*
     * The sender also receives its own message (broadcast), and history
     * may already contain it: never show the same id twice.
     */
    if (data.id && messages.value.some(item => item.id === data.id)) {
        return
    }

    // Play sound if it's not the current user and the conversation is not opened
    if (data.user_id !== authStore.sessionUser.id) {
        const audio = new Audio('/src/assets/sounds/notify.wav')
        audio.volume = 0.25
        audio.play()
    }

    messages.value.push({
        id: data.id ?? null,
        roomId: data.room_id,
        userId: data.user_id,
        content: data.content,
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

    /*
     * The server already knows who we are (ticket), so no user_id here.
     */
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
         * Ask the backend for the conversation. It finds the room between
         * the authenticated user and `user.id`, creating it if needed,
         * and returns `{ id }`.
         */
        const ok = await chatStore.conversation(user.id)
        if (!ok) {
            throw new Error(
                'Unable to open conversation.'
            )
        }

        /*
         * The user clicked someone else while we were waiting.
         */
        if (selectedChat.value?.id !== user.id) {
            return
        }

        currentRoomId.value = chatStore.conversationData.id

        /*
         * Join the WebSocket room first, then load history, so nothing
         * sent in between is lost. loadMessages() merges both.
         */
        joinRoom(currentRoomId.value)

        await loadMessages(currentRoomId.value)

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

    /*
     * Room changed while the request was in flight.
     */
    if (Number(roomId) !== Number(currentRoomId.value)) {
        return
    }

    const history = chatStore.messagesData.map(item => ({
        id: item.id,
        roomId: item.room_id,
        userId: item.user_id,
        content: item.content,
        createdAt: item.created_at,
    }))

    /*
     * Keep live messages that arrived while history was loading.
     */
    const live = messages.value.filter(
        item => !history.some(saved => saved.id === item.id)
    )

    messages.value = [...history, ...live]
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

    /*
     * Not added to the list here: the server persists it and broadcasts
     * the saved message back (with id and created_at), which is what we render.
     */
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
    unmounted = true

    if (reconnectTimer !== null) {
        clearTimeout(reconnectTimer)
        reconnectTimer = null
    }

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
                        <i v-if="status === 'Connected'" class="text-green-500 bi bi-circle-fill"></i>

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
                    <div class="flex flex-col gap-2 mb-2 p-2 border-stone-200 h-full min-h-80 max-h-[400px] overflow-y-scroll">
                        <div v-if="loadingConversation" class="text-stone-500 text-sm">
                            Loading conversation...
                        </div>

                        <div v-else-if="messages.length === 0 && selectedChat" class="text-stone-500 text-sm">
                            No messages yet.
                        </div>

                        <div class="right-0 bottom-0 absolute"> <i class="bi bi-arrow-down-circle-fill bi"></i> </div>
                        <ul class="flex flex-col gap-2 p-2 border-stone-200">
                            
                            <!-- Scroll to bottom  -->
                            
                            <li v-for="item in messages" :key="item.id ?? `${item.createdAt}-${item.content}`" class="flex flex-col flex-wrap p-2 rounded-md text-sm" 
                            :class="{ 'self-end bg-cyan-500': item.userId === authStore.sessionUser.id , 'self-start bg-emerald-500': item.userId !== authStore.sessionUser.id}" >
                                {{ item.content }}
                            </li>
                        </ul>

                    </div>

                    <!-- Input -->

                    <form v-if="selectedChat" @submit.prevent="sendMessage" class="flex flex-row justify-between gap-2">
                        <input v-model="message" class="p-2 border-stone-200 dark:border-stone-600 w-full" type="text" placeholder="Type a message..." :disabled="!connected ||
                            !currentRoomId ||
                            loadingConversation
                            " />

                        <button type="submit" class="bg-stone-100 hover:bg-stone-300 dark:bg-stone-800 dark:hover:bg-stone-700" :disabled="!connected ||
                            !currentRoomId ||
                            !message.trim() ||
                            sendingMessage
                            ">
                            <i class="mx-auto p-2 text-2xl bi bi-send-fill"></i>
                        </button>

                        <!-- audio recording button -->

                        <button type="button" @click="startRecording" v-show="!recording" class="bg-stone-100 hover:bg-stone-300 dark:bg-stone-800 dark:hover:bg-stone-700" :disabled="!connected ||
                            !currentRoomId ||
                            loadingConversation
                            ">
                            <i class="mx-auto p-2 text-2xl bi bi-mic-fill"></i>
                        </button>

                        <button type="button" @click="stopRecording" v-show="recording" class="bg-stone-100 hover:bg-stone-300 dark:bg-stone-800 dark:hover:bg-stone-700" :disabled="!connected ||
                            !currentRoomId ||
                            loadingConversation
                            ">
                            <i class="mx-auto p-2 text-2xl bi bi-mic-mute-fill"></i>
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
