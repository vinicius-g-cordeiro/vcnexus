<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-3xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Organization Branding</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Customize the system to the organization looks</p>
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div class="gap-4 grid grid-cols-1 sm:grid-cols-2">

                <div class="flex items-center gap-4 w-full">
                    <div class="group relative flex justify-center items-center bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full w-20 h-20 overflow-hidden cursor-pointer shrink-0" @click="triggerFilePicker">
                        <img v-if="logoPreview" :src="logoPreview" alt="Logo" class="w-full h-full object-cover" />
                        <i v-else class="text-zinc-400 text-3xl bi bi-buildings-fill"></i>
                        <div class="absolute inset-0 flex justify-center items-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="text-white bi bi-camera-fill"></i>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <button type="button" @click="triggerFilePicker" class="self-start bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-1.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 text-sm transition-colors">
                            Change logo
                        </button>
                        <button v-if="logoPreview" type="button" @click="removeLogo" class="self-start text-red-500 hover:text-red-600 text-xs">
                            Remove logo
                        </button>
                        <input ref="fileInput" type="file" accept="image/png, image/jpeg, image/webp" class="hidden" @change="handleFileChange" />
                    </div>
                </div>

                <Input v-model="form.app_name" type="text" label="Name" name="app_name" id="app_name" class="w-full" />

                <Select v-model="form.type" label="Type" name="type" id="type" :options="typeOptions" placeholder="Select" required />
                
                <Input v-model="form.website" type="text" label="Website" name="website" id="website" class="w-full" />
                
                <Input v-model="form.primary_color" type="color" label="Primary Color" name="primary_color" id="primary_color" class="w-full" />

                <Input v-model="form.secondary_color" type="color" label="Secondary Color" name="secondary_color" id="secondary_color" class="w-full" />

                <Input v-model="form.accent_color" type="color" label="Accent Color" name="accent_color" id="accent_color" class="w-full" />

                <Input v-model="form.background_color" type="color" label="Background Color" name="background_color" id="background_color" class="w-full" />

                <Input v-model="form.text_color" type="color" label="Text Color" name="text_color" id="text_color" class="w-full" />
            </div>

        </form>


    </div>
</template>

<script setup>
import { reactive, watch, ref, computed, onMounted } from 'vue'
import { useReferencesStore } from '@/stores/references/referencesStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    branding: { type: Array, default: () => [] },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const referencesStore = useReferencesStore()

const form = reactive({
    app_name: '',
    primary_color: '#3b82f6',
    secondary_color: '#f59e0b',
    accent_color: '#ef4444',
    text_color: '#000000',
    background_color: '#ffffff',
    font_style: 'nunito',
    button_style: '',
    logo: null,
    type: null
})


const fileInput = ref(null)
const logoPreview = ref('')
const logo = ref(null)

onMounted(async () => {
    await referencesStore.fetchSubscriptionPlans()
    await referencesStore.fetchSubscriptionStatuses()
    await referencesStore.fetchTenantTypes()
})

const typeOptions = computed(()=> referencesStore.tenant_types.map(t => ({ label: t.label, value: t.id })))


async function handleSubmit() {
    if (props.mode !== 'edit') return
}


function triggerFilePicker() {
    fileInput.value?.click()
}

function handleFileChange(e) {
    const file = e.target.files?.[0]
    if (!file) return
    logo.value = file
    logoPreview.value = URL.createObjectURL(file)
    if (props.mode === 'create') emit('update:modelValue', { ...form, logo: file })
}

function removeLogo() {
    logo.value = null
    logoPreview.value = ''
    if (fileInput.value) fileInput.value.value = ''
    if (props.mode === 'create') emit('update:modelValue', { ...form, logo: null })
}

</script>
