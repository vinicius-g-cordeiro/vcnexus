<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-3xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Contacts</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Emails, phones and other ways to reach this user</p>
            </div>
            <button type="button" @click="addContact" class="flex items-center gap-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-1.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 text-sm transition-colors">
                <i class="bi bi-plus-lg"></i> Add
            </button>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div v-for="(c, i) in form.list" :key="i" class="relative bg-zinc-50 dark:bg-zinc-800/40 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <button type="button" @click="form.list.splice(i, 1)" class="top-3 right-3 absolute text-zinc-400 hover:text-red-500 transition-colors">
                    <i class="bi bi-trash"></i>
                </button>
                <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                    <Select v-model="c.type" label="Type" :id="`type-${i}`" :options="typeOptions || []" placeholder="Select type" required />
                    <Input v-model="c.value" label="Value" :id="`value-${i}`" required />
                    <Input v-model="c.label" label="Label" placeholder="Mobile, Work..." :id="`label-${i}`" />
                    <Select v-model="c.category_id" label="Category" :id="`category-${i}`" :options="categoryOptions || []" placeholder="Select category" required />
                    <Input v-model="c.person" label="Person" placeholder="e.g. João" :id="`person-${i}`" />
                    <label class="flex items-center gap-2 mt-6">
                        <input type="checkbox" v-model="c.primary_contact" class="rounded w-4 h-4 accent-zinc-900 dark:accent-zinc-100" />
                        <span class="text-zinc-700 dark:text-zinc-300 text-sm">Primary contact</span>
                    </label>
                </div>
            </div>

            <p v-if="!form.list.length" class="py-6 text-zinc-400 text-sm text-center">No contacts added yet</p>

            <div v-if="mode === 'edit'" class="flex justify-end pt-2">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, watch, computed, onMounted} from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import { useReferencesStore } from '@/stores/references/referencesStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    contacts: { type: Array, default: () => [] },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()
const referencesStore = useReferencesStore()

const categoryOptions = computed(() => referencesStore.contact_categories?.map(c => ({ label: c.label, value: c.id })))
const typeOptions = computed(() => referencesStore.contact_types?.map(t => ({ label: t.label, value: t.id })))

const form = reactive({ list: [] })

if (props.mode === 'create') {
    form.list = (props.contacts ?? []).map(c => ({ ...c, primary_contact: !!c.primary_contact }))
    watch(form, (val) => emit('update:modelValue', val.list), { deep: true })
} else {
    watch(() => props.contacts, (val) => {
        form.list = (val ?? []).map(c => ({ ...c, primary_contact: !!c.primary_contact }))
    }, { immediate: true })
}


onMounted(async () => {
    await referencesStore.fetchContactCategories()
    console.log(referencesStore.contact_categories)
    await referencesStore.fetchContactTypes()
    console.log(referencesStore.contact_types)
})

function addContact() {
    form.list.push({
        type: '',
        value: '',
        label: '',
        category: '',
        person: '',
        primary_contact: false,
    })
}

async function handleSubmit() {
    if (props.mode !== 'edit') return
    await userStore.updateContacts(route.params.uuid, form.list)
}
</script>