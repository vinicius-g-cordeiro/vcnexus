<script setup>
import { onBeforeUnmount, ref } from 'vue'

const socket = ref(null)
const status = ref('Disconnected')
const message = ref('')
const messages = ref([])

function connect() {
    if (socket.value?.readyState === WebSocket.OPEN) {
        return
    }

    status.value = 'Connecting...'

    socket.value = new WebSocket('ws://localhost:8080')

    socket.value.onopen = () => {
        status.value = 'Connected'
        addMessage('SYSTEM', 'WebSocket connected')
    }

  socket.value.onmessage = (event) => {
    const payload = JSON.parse(event.data)

    messages.value.push({
        sender: `CLIENT ${payload.data.from}`,
        content: payload.data.message,
        time: new Date().toLocaleTimeString(),
    })
}

    socket.value.onerror = () => {
        status.value = 'Error'
        addMessage('SYSTEM', 'WebSocket error')
    }

    socket.value.onclose = () => {
        status.value = 'Disconnected'
        addMessage('SYSTEM', 'WebSocket disconnected')
    }
}

function sendMessage() {
     if (socket.value?.readyState !== WebSocket.OPEN) {
        return
    }

    if (!message.value.trim()) {
        return
    }

    socket.value.send(JSON.stringify({
        message: message.value,
    }))

    message.value = ''
}

function disconnect() {
    socket.value?.close()
}

function addMessage(sender, content) {
    messages.value.push({
        sender,
        content,
        time: new Date().toLocaleTimeString(),
    })
}

onBeforeUnmount(() => {
    socket.value?.close()
})
</script>

<template>
    <main class="websocket-test">
        <h1>WebSocket Test</h1>

        <p>
            Status:
            <strong>{{ status }}</strong>
        </p>

        <div class="controls">
            <button type="button" @click="connect" :disabled="status === 'Connected'">
                Connect
            </button>

            <button type="button" @click="disconnect" :disabled="status !== 'Connected'">
                Disconnect
            </button>
        </div>

        <div class="messages">
            <div v-for="(item, index) in messages" :key="index" class="message">
                <strong>[{{ item.time }}] {{ item.sender }}:</strong>
                {{ item.content }}
            </div>
        </div>

        <form @submit.prevent="sendMessage">
            <input v-model="message" type="text" placeholder="Type a message..." :disabled="status !== 'Connected'" />

            <button type="submit" :disabled="status !== 'Connected' || !message.trim()">
                Send
            </button>
        </form>
    </main>
</template>

<style scoped>
.websocket-test {
    max-width: 700px;
    margin: 40px auto;
    padding: 24px;
    font-family: sans-serif;
}

.controls {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
}

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

form {
    display: flex;
    gap: 8px;
}

input {
    flex: 1;
    padding: 8px;
}

button {
    padding: 8px 16px;
    cursor: pointer;
}
</style>