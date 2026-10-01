<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-3xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Consents</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Legal basis and consent records (LGPD/GDPR)</p>
            </div>
            <button type="button" @click="addConsent" class="flex items-center gap-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-1.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 text-sm transition-colors">
                <i class="bi bi-plus-lg"></i> Add
            </button>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div v-for="(c, i) in form.list" :key="i" class="relative bg-zinc-50 dark:bg-zinc-800/40 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <button type="button" @click="form.list.splice(i, 1)" class="top-3 right-3 absolute text-zinc-400 hover:text-red-500 transition-colors">
                    <i class="bi bi-trash"></i>
                </button>
                <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                    <Input v-model="c.purpose" label="Purpose" placeholder="Diversity reporting..." :id="`purpose-${i}`" required />
                    <Select v-model="c.legal_basis" label="Legal Basis" :id="`basis-${i}`" :options="basisOptions" placeholder="Select basis" required />
                    <Input v-model="c.granted_at" type="datetime-local" label="Granted At" :id="`granted-${i}`" />
                    <Input v-model="c.revoked_at" type="datetime-local" label="Revoked At" :id="`revoked-${i}`" />
                </div>
            </div>

            <p v-if="!form.list.length" class="py-6 text-zinc-400 text-sm text-center">No consent records yet</p>

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
    consents: { type: Array, default: () => [] }
})

const route = useRoute()
const userStore = useUserStore()

const basisOptions = [
    { value: 'consent', label: 'Consent' },
    { value: 'legal obligation', label: 'Legal Obligation' },
    { value: 'legitimate interest', label: 'Legitimate Interest' },
    { value: 'contract', label: 'Contract' },
]

const form = reactive({ list: [] })

watch(() => props.consents, (val) => {
    form.list = (val ?? []).map(c => ({ ...c }))
}, { immediate: true })

function addConsent() {
    form.list.push({
        purpose: '',
        legal_basis: '',
        granted_at: '',
        revoked_at: '',
    })
}

async function handleSubmit() {
    await userStore.updateConsents(route.params.uuid, form.list)
}
</script>