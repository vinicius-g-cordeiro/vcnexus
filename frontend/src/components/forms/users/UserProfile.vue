<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-2xl">
        <div>
            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Profile</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Personal information</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="group relative flex justify-center items-center bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full w-20 h-20 overflow-hidden cursor-pointer shrink-0" @click="triggerFilePicker">
                <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
                <i v-else class="text-zinc-400 text-3xl bi bi-person-fill"></i>
                <div class="absolute inset-0 flex justify-center items-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="text-white bi bi-camera-fill"></i>
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <button type="button" @click="triggerFilePicker" class="self-start bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-1.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 text-sm transition-colors">
                    Change Avatar
                </button>
                <button v-if="avatarPreview" type="button" @click="removeAvatar" class="self-start text-red-500 hover:text-red-600 text-xs">
                    Remove photo
                </button>
                <input ref="fileInput" type="file" accept="image/png, image/jpeg, image/webp" class="hidden" @change="handleFileChange" />
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-2">
            <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                <Input v-model="form.firstname" type="text" label="First Name" name="firstname" id="firstname" required />
                <Input v-model="form.surname" type="text" label="Surname" name="surname" id="surname" />
                <Input v-model="form.lastname" type="text" label="Last Name" name="lastname" id="lastname" required />
                <Input v-model="form.birthdate" type="date" label="Birth Date" name="birthdate" id="birthdate" />
                <Select v-model="form.locale" label="Locale" name="locale" id="locale" :options="localeOptions" placeholder="Select locale" />
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
import { reactive, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    profile: { type: Object, default: () => ({}) },
    mode: { type: String, default: 'edit' }, // 'edit' | 'create'
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()

const localeOptions = [
    { value: 'en-US', label: 'English (US)' },
    { value: 'pt-BR', label: 'Português (BR)' },
    { value: 'es-ES', label: 'Español' },
]

function formatDate(date){
    if(date === undefined || date===null) return ''

    const d = new Date(date)
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')

    return `${year}-${month}-${day}`
}

const form = reactive({
    firstname: '',
    surname: '',
    lastname: '',
    birthdate: '',
    locale: 'en-US',
})

const fileInput = ref(null)
const avatarPreview = ref('')
const avatarFile = ref(null)

function seedFromProfile(val) {
    form.firstname = val?.firstname ?? ''
    form.surname = val?.surname ?? ''
    form.lastname = val?.lastname ?? ''
    form.birthdate = formatDate(val?.birthdate) ?? ''
    form.locale = val?.locale ?? 'en-US'
    avatarPreview.value = val?.avatar ?? ''
    avatarFile.value = val?.avatarFile ?? null
}

if (props.mode === 'create') {
    // seed once from whatever draft the parent already has (e.g. returning to
    // this tab after visiting another one), then stop listening — the prop
    // and local form stay in sync via the emit below, so re-watching the prop
    // would just echo our own edits back and reset mid-keystroke
    seedFromProfile(props.profile)
    watch(form, (val) => {
        emit('update:modelValue', { ...val, birthdate: formatDate(val.birthdate) ?? '', avatarFile: avatarFile.value })
    }, { deep: true })
} else {
    // edit mode: the prop comes from the store and can legitimately change
    // underneath us (e.g. a fresh fetch), so keep reacting to it
    watch(() => props.profile, seedFromProfile, { immediate: true })
}

function triggerFilePicker() {
    fileInput.value?.click()
}

function handleFileChange(e) {
    const file = e.target.files?.[0]
    if (!file) return
    avatarFile.value = file
    avatarPreview.value = URL.createObjectURL(file)
    if (props.mode === 'create') emit('update:modelValue', { ...form, avatarFile: file })
}

function removeAvatar() {
    avatarFile.value = null
    avatarPreview.value = ''
    if (fileInput.value) fileInput.value.value = ''
    if (props.mode === 'create') emit('update:modelValue', { ...form, avatarFile: null })
}

// edit mode only — create mode never calls the store, the parent does on final submit
async function handleSubmit() {
    if (props.mode !== 'edit') return
    const payload = new FormData()
    Object.entries(form).forEach(([key, value]) => payload.append(key, value ?? ''))
    if (avatarFile.value) {
        payload.append('avatar', avatarFile.value)
    } else if (!avatarPreview.value) {
        payload.append('avatar_removed', '1')
    }
    await userStore.updateProfile(route.params.uuid, payload)
}
</script>