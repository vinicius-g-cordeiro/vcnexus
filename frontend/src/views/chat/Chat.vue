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
    <main class="right-6 bottom-6 fixed flex flex-col gap-2 bg-zinc-100 dark:bg-zinc-800 shadow-xl p-2 border-zinc-200 w-4/12">
        <section class="flex flex-row justify-between items-center">
            <h1 class="font-bold text-4xl sm:text-5xl tracking-tight">
                Chat
            </h1>
            <button type="button" @click="toggleChat" class="bg-zinc-100 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700">
                <i class="mx-auto p-2 text-2xl bi bi-dash-lg"></i>
            </button>
        </section>
        <section class="flex flex-row justify-between gap-2">
            <aside class="flex flex-col bg-zinc-100 dark:bg-zinc-700 w-1/3">
                <div v-if="users.length === 0" class="p-3 text-zinc-500 text-sm">
                    No users available.
                </div>
                <button v-for="user in users" :key="user.id" type="button" @click="changeConversation(user)" class="flex flex-row justify-between items-center hover:bg-zinc-200 dark:hover:bg-zinc-600 p-2 border-zinc-200 border-b text-left">
                    <template v-if="user.avatar">
                        <img class="rounded-full w-8 h-8" :src="user.avatar" :alt="user.name">
                    </template>
                    <template v-else>
                        <span class="flex justify-center items-center bg-zinc-500 dark:bg-zinc-600 mr-2 rounded-full w-8 h-8 font-medium text-zinc-100 dark:text-zinc-400 text-sm">
                            <i class="bi bi-person-fill"></i>
                        </span>
                    </template>
                    <span class="self-center font-semibold dark:text-white text-sm">
                        {{ user.name }} {{ user.surname }}
                    </span>
                    <span>
                        <i v-if="user.online_status === 1" class="text-green-500 bi bi-circle-fill"></i>
                        <i v-else-if="user.online_status === 2" class="text-orange-500 bi bi-circle-fill"></i>
                        <i v-else-if="user.online_status === 3" class="text-red-500 bi bi-circle-fill"></i>
                        <i v-else class="text-zinc-500 bi bi-circle-fill"></i>
                    </span>
                </button>
            </aside>
            <div class="flex flex-col gap-2 w-2/3">
                <div v-if="selectedChat" class="flex flex-col gap-2 font-semibold dark:text-white text-sm whitespace-nowrap">
                    <section class="flex flex-row items-center">
                        <template v-if="selectedChat?.avatar">
                            <img class="rounded-full w-8 h-8" :src="selectedChat?.avatar" :alt="selectedChat?.name">
                        </template>
                        <template v-else>
                            <span class="flex justify-center items-center bg-zinc-500 dark:bg-zinc-600 mr-2 rounded-full w-8 h-8 font-medium text-zinc-100 dark:text-zinc-400 text-sm">
                                <i class="bi bi-person-fill"></i>
                            </span>
                        </template>
                        <strong>
                            {{ selectedChat.name }} {{ selectedChat.id === authStore.sessionUser.id ? '(You)' : '' }}
                        </strong>
                    </section>
                    <p class="justify-self-start text-zinc-500 text-xs whitespace-nowrap">
                        <i v-if="selectedChat.online_status === 1"> Online </i>
                        <i v-else-if="selectedChat.online_status === 2"> Away </i>
                        <i v-else-if="selectedChat.online_status === 3"> Busy </i>
                        <i v-else> Offline </i>
                    </p>
                </div>
                <p v-else class="self-center text-zinc-500 text-sm">
                    Select a user.
                </p>
                <div class="flex flex-col gap-2 bg-zinc-100 dark:bg-zinc-700">
                    <div ref="chatContainerRef" @scroll="handleMessageScroll" class="relative flex flex-col gap-2 mb-2 p-2 border-zinc-200 h-full min-h-80 max-h-[400px] overflow-y-auto">
                        <div v-if="loadingConversation" class="text-zinc-500 text-sm">
                            Loading conversation...
                        </div>
                        <div v-else-if="messages.length === 0 && selectedChat" class="text-zinc-500 text-sm">
                            No messages yet.
                        </div>
                        <ul class="flex flex-col gap-2 p-2 border-zinc-200 h-full">
                            <li v-for="item in messages" :key="item.id ?? `${item.createdAt}-${item.content}`" class="flex flex-col px-3 py-2 rounded-2xl max-w-[75%] text-sm break-words whitespace-pre-wrap" :class="{
                                'self-end rounded-br-md bg-cyan-500 dark:bg-cyan-600 text-white':
                                    Number(item.userId) === Number(authStore.sessionUser.id),
                                'self-start rounded-bl-md bg-zinc-200 dark:bg-zinc-600 dark:text-white':
                                    Number(item.userId) !== Number(authStore.sessionUser.id)
                            }">
                                <template v-if="item.type === 'audio'">
                                    <audio controls preload="metadata" :src="item.content" class="max-w-full max-h-12 object-center object-contain">
                                    </audio>
                                </template>
                                <template v-else>
                                    {{ item.content }}
                                </template>
                            </li>
                        </ul>
                        <div class="right-5 bottom-5 z-10 absolute flex">
                            <button v-if="showScrollToBottom" type="button" @click="scrollToBottom()" class="justify-center items-center bg-zinc-800 hover:bg-zinc-700 shadow-lg rounded-full w-9 h-9 text-white" aria-label="Scroll to bottom">
                                <i class="bi bi-arrow-down"></i>
                            </button>
                        </div>
                    </div>
                    <form v-if="selectedChat" @submit.prevent="sendMessage" class="flex flex-row justify-between items-center gap-2 bg-zinc-600 dark:bg-zinc-900">
                        <textarea v-model="message" id="message" class="p-2 border-zinc-200 dark:border-zinc-600 w-full text-sm resize-none" type="text" placeholder="Type a message..." :disabled="!connected ||
                            !currentRoomId ||
                            loadingConversation ||
                            !isMicrophoneAccessible
                            " @keydown.exact.enter="sendMessage" @keydown.enter.exact.prevent>
            </textarea>
                        <button type="submit" class="bg-zinc-100 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700" :disabled="!connected ||
                            !currentRoomId ||
                            !message.trim() ||
                            sendingMessage
                            ">
                            <i class="mx-auto p-2 text-2xl bi bi-send-fill"></i>
                        </button>
                        <select id="microphone" v-model="selectedMicrophone" class="bg-zinc-100 dark:bg-zinc-800 p-2 border-zinc-200 dark:border-zinc-600 w-2/6 text-zinc-500 text-xs">
                            <option v-for="microphone in microphones" :key="microphone.deviceId" :value="microphone.deviceId">
                                {{ microphone.label || 'Microphone' }}
                            </option>
                        </select>
                        <button type="button" @click="startRecording" v-show="!recording" class="bg-zinc-100 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700" :disabled="!connected ||
                            !currentRoomId ||
                            loadingConversation ||
                            !isMicrophoneAccessible
                            ">
                            <i class="mx-auto p-2 text-2xl bi bi-mic-fill"></i>
                        </button>
                        <button type="button" @click="stopRecording" v-show="recording" class="bg-zinc-100 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700" :disabled="!connected ||
                            !currentRoomId ||
                            loadingConversation
                            ">
                            <i class="mx-auto p-2 text-2xl bi bi-mic-mute-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <small class="text-zinc-500">
            {{ status }}
        </small>
    </main>
</template>