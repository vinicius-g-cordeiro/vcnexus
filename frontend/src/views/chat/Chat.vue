<script setup>
import {
    computed,
    defineEmits,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
} from 'vue'
import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { useChatStore } from '@/stores/chat/chatStore'
import { useAudioDevices } from '@/composables/useAudioDevices'
const {
    microphones,
    loadMicrophones,
    requestMicrophonePermission
} = useAudioDevices()
const selectedMicrophone = ref('')
onMounted(async () => {
    try {
        await requestMicrophonePermission()
        if (microphones.value.length > 0) {
            selectedMicrophone.value = microphones.value[0].deviceId
        }
    } catch (error) {
        console.error('Unable to access microphones:', error)
    }
})
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
const mediaRecorder = ref(null)
const audioChunks = ref([])
const showScrollToBottom = ref(false)
function addNewLine() {
    message.value += "\r\n";
}
const isRecording = computed(() => {
    return recording.value
})
function scrollToBottom(smooth = true) {
    const container = chatContainerRef.value
    if (!container) {
        return
    }
    container.scrollTo({
        top: container.scrollHeight,
        behavior: smooth ? 'smooth' : 'auto',
    })
    showScrollToBottom.value = false
}
function handleMessageScroll() {
    const container = chatContainerRef.value
    if (!container) {
        return
    }
    const distanceFromBottom =
        container.scrollHeight -
        container.scrollTop -
        container.clientHeight
    showScrollToBottom.value = distanceFromBottom > 50
}
const isMicrophoneAccessible = ref(false)
isMicrophoneAccessible.value = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
const isChatOpen = ref(false)
const currentRoom = computed(() => {
    return currentRoomId.value
})
let unmounted = false
let reconnectAttempts = 0
let reconnectTimer = null
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
        if (!unmounted) {
            scheduleReconnect()
        }
    }
}
function scheduleReconnect() {
    if (unmounted || reconnectTimer !== null) {
        return
    }
    const delay = Math.min(1000 * 2 ** reconnectAttempts, 15000)
    reconnectAttempts++
    reconnectTimer = setTimeout(() => {
        reconnectTimer = null
        connect()
    }, delay)
}
async function resumeRoom() {
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
    if (Number(data.room_id) !== Number(currentRoomId.value)) {
        return
    }
    if (data.id && messages.value.some(item => item.id === data.id)) {
        return
    }
    const userId = Number(data.user_id)
    if (userId !== Number(authStore.sessionUser.id)) {
        const audio = new Audio('/src/assets/sounds/notify.wav')
        audio.volume = 0.25
        audio.play().catch(() => { })
    }
    messages.value.push({
        id: data.id ?? null,
        roomId: Number(data.room_id),
        userId,
        content: data.content,
        type: data.type ?? 'text',
        createdAt: data.created_at ?? new Date().toISOString(),
    })
    if (userId === Number(authStore.sessionUser.id)) {
        scrollToBottom()
    }
}
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
async function changeConversation(user) {
    if (!user?.id) {
        return
    }
    if (selectedChat.value?.id === user.id) {
        return
    }
    loadingConversation.value = true
    try {
        if (currentRoomId.value !== null) {
            leaveRoom(currentRoomId.value)
        }
        selectedChat.value = user
        currentRoomId.value = null
        messages.value = []
        const ok = await chatStore.conversation(user.id)
        if (!ok) {
            throw new Error(
                'Unable to open conversation.'
            )
        }
        if (selectedChat.value?.id !== user.id) {
            return
        }
        currentRoomId.value = chatStore.conversationData.id
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
async function loadMessages(roomId) {
    const ok = await chatStore.messages(roomId)
    if (!ok) {
        throw new Error(
            'Unable to load messages.'
        )
    }
    if (Number(roomId) !== Number(currentRoomId.value)) {
        return
    }
    const history = chatStore.messagesData.map(item => ({
        id: item.id,
        roomId: Number(item.room_id),
        userId: Number(item.user_id),
        content: item.content,
        type: item.type ?? 'text',
        createdAt: item.created_at,
    }))
    const live = messages.value.filter(
        item => !history.some(saved => saved.id === item.id)
    )
    messages.value = [...history, ...live]
    await nextTick()
}
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
async function startRecording() {
    await loadMicrophones()
    const constraints = {
        audio: {
            deviceId: selectedMicrophone.value
                ? { exact: selectedMicrophone.value }
                : undefined,
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true
        }
    }
    const stream = await navigator.mediaDevices.getUserMedia(constraints)
    const recorder = new MediaRecorder(
        stream,
        {
            mimeType: getSupportedAudioMimeType()
        }
    )
    mediaRecorder.value = recorder
    audioChunks.value = []
    recorder.ondataavailable = event => {
        if (event.data.size > 0) {
            audioChunks.value.push(event.data)
        }
    }
    recorder.onstop = async () => {
        const blob = new Blob(audioChunks.value, {
            type: recorder.mimeType
        })
        await sendAudioMessage(blob)
        stream.getTracks().forEach(track => track.stop())
    }
    recorder.start()
    recording.value = true
}
function stopRecording() {
    if (
        mediaRecorder.value &&
        mediaRecorder.value.state === 'recording'
    ) {
        mediaRecorder.value.stop()
    }
}
function getSupportedAudioMimeType() {
    const types = [
        'audio/webm;codecs=opus',
        'audio/webm',
    ]
    return types.find(type => MediaRecorder.isTypeSupported(type)) ?? ''
}
async function sendAudioMessage(blob) {
    if (
        !connected.value ||
        !currentRoomId.value ||
        !socket.value ||
        socket.value.readyState !== WebSocket.OPEN
    ) {
        return
    }
    const form = new FormData()
    form.append('room_id', currentRoomId.value)
    form.append('user_id', authStore.sessionUser.id)
    form.append('tenant_id', authStore.sessionUser?.tenants)
    form.append('content_type', 'audio/webm')
    form.append('filename', 'audio.webm')
    form.append('content_length', blob.size)
    form.append('file', blob, 'audio.webm')
    recording.value = false
    const response = await chatStore.sendAudioMessage(form)
    if (response.error) {
        console.error('Unable to send audio message:', response.error)
    }
    socket.value.send(JSON.stringify({
        action: 'send_message',
        room_id: currentRoomId.value,
        type: 'audio',
        message: response,
    }))
}
async function loadUsers() {
    try {
        const ok = await chatStore.users();
        if (!ok) {
            throw new Error(
                'Unable to load chat users.'
            )
        }
        const data = chatStore.usersData
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
const toggleChat = (event) => {
    emit('click', event)
}
</script>

<template>
    <main class="right-6 bottom-6 fixed flex flex-col bg-white dark:bg-zinc-900 shadow-2xl border border-zinc-200 dark:border-zinc-700 rounded-2xl w-[34rem] max-w-[calc(100vw-2rem)] h-[34rem] max-h-[calc(100vh-3rem)] overflow-hidden">
        <header class="flex flex-row justify-between items-center bg-olive-wood-500 px-4 py-3 text-white">
            <div class="flex flex-row items-center gap-2">
                <i class="text-lg bi bi-chat-dots-fill"></i>
                <h1 class="font-semibold text-base tracking-tight">Chat</h1>
                <span class="flex items-center gap-1.5 ml-2 text-white/80 text-xs">
                    <span class="rounded-full w-1.5 h-1.5" :class="connected ? 'bg-emerald-300' : 'bg-red-300'"></span>
                    {{ status }}
                </span>
            </div>
            <button type="button" @click="toggleChat" class="flex justify-center items-center hover:bg-white/15 rounded-lg w-8 h-8 transition" aria-label="Minimize chat">
                <i class="bi bi-dash-lg"></i>
            </button>
        </header>

        <section class="flex flex-row flex-1 min-h-0">
            <aside class="flex flex-col bg-zinc-50 dark:bg-zinc-800/60 border-zinc-200 dark:border-zinc-700 border-r w-2/5 sm:w-1/3 overflow-y-auto">
                <div v-if="users.length === 0" class="p-4 text-zinc-500 dark:text-zinc-400 text-sm text-center">
                    No users available.
                </div>

                <button v-for="user in users" :key="user.id" type="button" @click="changeConversation(user)" class="flex flex-row items-center gap-3 px-3 py-2.5 border-zinc-200 dark:border-zinc-700/60 border-b text-left transition" :class="selectedChat?.id === user.id
                    ? 'bg-olive-wood-500/10 dark:bg-olive-wood-500/20'
                    : 'hover:bg-zinc-100 dark:hover:bg-zinc-700/50'">
                    <div class="relative shrink-0">
                        <img v-if="user.avatar" class="rounded-full w-9 h-9 object-cover" :src="user.avatar" :alt="user.name">
                        <span v-else class="flex justify-center items-center bg-zinc-300 dark:bg-zinc-600 rounded-full w-9 h-9 text-zinc-600 dark:text-zinc-300 text-sm">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <span class="right-0 bottom-0 absolute border-2 border-zinc-50 dark:border-zinc-800 rounded-full w-3 h-3" :class="{
                            'bg-emerald-500': user.online_status === 1,
                            'bg-orange-400': user.online_status === 2,
                            'bg-red-500': user.online_status === 3,
                            'bg-zinc-400': ![1, 2, 3].includes(user.online_status)
                        }"></span>
                    </div>
                    <span class="font-medium text-zinc-800 dark:text-zinc-100 text-sm truncate">
                        {{ user.name }} {{ user.surname }}
                    </span>
                </button>
            </aside>

            <div class="flex flex-col flex-1 min-w-0">
                <div v-if="selectedChat" class="flex flex-row items-center gap-3 px-4 py-2.5 border-zinc-200 dark:border-zinc-700 border-b">
                    <img v-if="selectedChat?.avatar" class="rounded-full w-9 h-9 object-cover" :src="selectedChat?.avatar" :alt="selectedChat?.name">
                    <span v-else class="flex justify-center items-center bg-zinc-300 dark:bg-zinc-600 rounded-full w-9 h-9 text-zinc-600 dark:text-zinc-300 text-sm">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <div class="flex flex-col min-w-0">
                        <strong class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm truncate">
                            {{ selectedChat.name }} {{ selectedChat.id === authStore.sessionUser.id ? '(You)' : '' }}
                        </strong>
                        <span class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400 text-xs">
                            <span class="rounded-full w-1.5 h-1.5" :class="{
                                'bg-emerald-500': selectedChat.online_status === 1,
                                'bg-orange-400': selectedChat.online_status === 2,
                                'bg-red-500': selectedChat.online_status === 3,
                                'bg-zinc-400': ![1, 2, 3].includes(selectedChat.online_status)
                            }"></span>
                            <template v-if="selectedChat.online_status === 1">Online</template>
                            <template v-else-if="selectedChat.online_status === 2">Away</template>
                            <template v-else-if="selectedChat.online_status === 3">Busy</template>
                            <template v-else>Offline</template>
                        </span>
                    </div>
                </div>

                <div v-else class="flex flex-col flex-1 justify-center items-center gap-2 text-zinc-400 dark:text-zinc-500">
                    <i class="text-4xl bi bi-chat-square-text"></i>
                    <p class="text-sm">Select a user to start chatting.</p>
                </div>

                <div v-if="selectedChat" class="relative flex flex-col flex-1 bg-zinc-50 dark:bg-zinc-950/40 min-h-0">
                    <div ref="chatContainerRef" @scroll="handleMessageScroll" class="flex flex-col flex-1 gap-2 p-4 overflow-y-auto">
                        <div v-if="loadingConversation" class="py-4 text-zinc-500 dark:text-zinc-400 text-sm text-center">
                            Loading conversation...
                        </div>
                        <div v-else-if="messages.length === 0" class="py-4 text-zinc-500 dark:text-zinc-400 text-sm text-center">
                            No messages yet. Say hi.
                        </div>

                        <ul class="flex flex-col gap-1.5">
                            <li v-for="item in messages" :key="item.id ?? `${item.createdAt}-${item.content}`" class="flex flex-col shadow-sm px-3.5 py-2 rounded-2xl max-w-[80%] text-sm break-words whitespace-pre-wrap" :class="{
                                'self-end rounded-br-md bg-olive-wood-500 text-white':
                                    Number(item.userId) === Number(authStore.sessionUser.id),
                                'self-start rounded-bl-md bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-600':
                                    Number(item.userId) !== Number(authStore.sessionUser.id)
                            }">
                                <template v-if="item.type === 'audio'">
                                    <audio controls preload="metadata" :src="item.content" class="w-56 max-w-full h-10"></audio>
                                </template>
                                <template v-else>
                                    {{ item.content }}
                                </template>
                            </li>
                        </ul>
                    </div>

                    <button v-if="showScrollToBottom" type="button" @click="scrollToBottom()" class="right-4 bottom-3 absolute flex justify-center items-center bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-600 dark:hover:bg-zinc-500 shadow-lg rounded-full w-9 h-9 text-white transition" aria-label="Scroll to bottom">
                        <i class="bi bi-arrow-down"></i>
                    </button>
                </div>

                <form v-if="selectedChat" @submit.prevent="sendMessage" class="flex flex-row items-end gap-2 bg-white dark:bg-zinc-900 p-3 border-zinc-200 dark:border-zinc-700 border-t">
                    <textarea v-model="message" id="message" rows="1" class="flex-1 bg-zinc-100 dark:bg-zinc-800 disabled:opacity-50 px-3.5 py-2.5 rounded-2xl outline-none focus:ring-2 focus:ring-olive-wood-500/50 min-w-0 max-h-28 dark:text-zinc-100 text-sm resize-none placeholder-zinc-400" placeholder="Type a message..." :disabled="!connected ||
                        !currentRoomId ||
                        loadingConversation ||
                        !isMicrophoneAccessible
                        " @keydown.exact.enter="sendMessage" @keydown.enter.exact.prevent></textarea>

                    <select id="microphone" v-model="selectedMicrophone" class="hidden sm:block bg-zinc-100 dark:bg-zinc-800 px-2 py-2.5 rounded-xl outline-none focus:ring-2 focus:ring-olive-wood-500/50 w-24 text-zinc-500 dark:text-zinc-400 text-xs truncate">
                        <option v-for="microphone in microphones" :key="microphone.deviceId" :value="microphone.deviceId">
                            {{ microphone.label || 'Microphone' }}
                        </option>
                    </select>

                    <button type="button" @click="startRecording" v-show="!recording" class="flex justify-center items-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 disabled:opacity-40 rounded-full w-10 h-10 text-zinc-600 dark:text-zinc-300 transition disabled:cursor-not-allowed" :disabled="!connected ||
                        !currentRoomId ||
                        loadingConversation ||
                        !isMicrophoneAccessible
                        " aria-label="Start recording">
                        <i class="bi bi-mic-fill"></i>
                    </button>

                    <button type="button" @click="stopRecording" v-show="recording" class="flex justify-center items-center bg-red-500 hover:bg-red-600 disabled:opacity-40 rounded-full w-10 h-10 text-white transition animate-pulse disabled:cursor-not-allowed" :disabled="!connected ||
                        !currentRoomId ||
                        loadingConversation
                        " aria-label="Stop recording">
                        <i class="bi bi-stop-fill"></i>
                    </button>

                    <button type="submit" class="flex justify-center items-center bg-olive-wood-500 hover:bg-olive-wood-600 disabled:opacity-40 rounded-full w-10 h-10 text-white transition disabled:cursor-not-allowed" :disabled="!connected ||
                        !currentRoomId ||
                        !message.trim() ||
                        sendingMessage
                        " aria-label="Send message">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        </section>
    </main>
</template>