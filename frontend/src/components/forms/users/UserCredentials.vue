<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-2xl">
        <div>
            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Credentials</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Login and account security</p>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-2">
            <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                <Input v-model="form.username" type="text" label="Username" name="username" id="username" required />
                <Input v-model="form.email" type="email" label="Email" name="email" id="email" required />
                <Input v-model="form.password" type="password" :label="mode === 'create' ? 'Password' : 'New Password'" name="password" id="password" showPassword passwordMeter :required="mode === 'create'" />
                
            </div>

            <div v-if="mode === 'edit' && form.last_login_at" class="gap-2 grid grid-cols-1 sm:grid-cols-2 mt-2 pt-4 border-zinc-200 dark:border-zinc-800 border-t text-zinc-500 dark:text-zinc-400 text-xs">
                <span>Last login: {{ form.last_login_at }}</span>
                <span>IP: {{ form.last_login_ip }}</span>
            </div>

            <div v-if="mode === 'edit'" class="flex justify-end pt-2">
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
    credentials: { type: Object, default: () => ({}) },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()


const form = reactive({
    username: '',
    email: '',
    password: '',
    last_login_at: '',
    last_login_ip: '',
})

function seedFromCredentials(val, { clearPassword } = {}) {
    form.username = val?.username ?? ''
    form.email = val?.email ?? ''
    if (clearPassword) form.password = ''
    else form.password = val?.password ?? ''
    form.last_login_at = val?.last_login_at ?? ''
    form.last_login_ip = val?.last_login_ip ?? ''
}

if (props.mode === 'create') {
    seedFromCredentials(props.credentials)
    watch(form, (val) => emit('update:modelValue', { ...val }), { deep: true })
} else {
    watch(() => props.credentials, (val) => seedFromCredentials(val, { clearPassword: true }), { immediate: true })
}

async function handleSubmit() {
    if (props.mode !== 'edit') return
    await userStore.updateCredentials(route.params.uuid, { ...form })
}
</script>