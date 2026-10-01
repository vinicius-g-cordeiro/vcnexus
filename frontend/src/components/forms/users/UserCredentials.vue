<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-2xl">
        <div>
            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Credentials</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Login and account security</p>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-2" autocomplete="off">
            <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                <Input v-model="form.email" type="email" label="Email" name="email" id="email" required />
                <Input v-model="form.password" autocomplete="new-password" type="password" label="New Password" name="password" id="password" showPassword passwordMeter />
                <Select v-model="form.status" label="Status" name="status" id="status" :options="statusOptions" placeholder="Select status" />
                <Select v-model="form.blocked" label="Blocked" name="blocked" id="blocked" :options="yesNoOptions" placeholder="Select" />
                <Select v-model="form.remember" label="Remember Login" name="remember" id="remember" :options="yesNoOptions" placeholder="Select" />
            </div>

            <div v-if="form.last_login_at" class="gap-2 grid grid-cols-1 sm:grid-cols-2 mt-2 pt-4 border-zinc-200 dark:border-zinc-800 border-t text-zinc-500 dark:text-zinc-400 text-xs">
                <span>Last login: {{ form.last_login_at }}</span>
                <span>IP: {{ form.last_login_ip }}</span>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    credentials: { type: Object, default: () => ({}) }
})

const route = useRoute()
const userStore = useUserStore()

const statusOptions = [
    { value: '1', label: 'Online' },
    { value: '2', label: 'Away' },
    { value: '3', label: 'Busy' },
    { value: '0', label: 'Offline' },
]

const yesNoOptions = [
    { value: '1', label: 'Yes' },
    { value: '0', label: 'No' },
]

const form = reactive({
    email: '',
    password: '',
    blocked: '',
    remember: '',
    status: '',
    last_login_at: '',
    last_login_ip: '',
})

watch(() => props.credentials, (val) => {
    form.email = val?.email ?? ''
    form.password = ''
    form.blocked = val?.blocked != null ? String(val.blocked) : ''
    form.remember = val?.remember != null ? String(val.remember) : ''
    form.status = val?.status != null ? String(val.status) : ''
    form.last_login_at = val?.last_login_at ?? ''
    form.last_login_ip = val?.last_login_ip ?? ''
}, { immediate: true })

async function handleSubmit() {
    await userStore.updateCredentials(route.params.uuid, { ...form })
}
</script>