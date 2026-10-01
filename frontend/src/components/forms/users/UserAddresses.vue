<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-3xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Addresses</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Where this user can be reached physically</p>
            </div>
            <button type="button" @click="addAddress" class="flex items-center gap-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-1.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 text-sm transition-colors">
                <i class="bi bi-plus-lg"></i> Add
            </button>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div v-for="(addr, i) in form.list" :key="i" class="relative bg-zinc-50 dark:bg-zinc-800/40 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <button type="button" @click="form.list.splice(i, 1)" class="top-3 right-3 absolute text-zinc-400 hover:text-red-500 transition-colors">
                    <i class="bi bi-trash"></i>
                </button>
                <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                    <Input v-model="addr.purpose" label="Purpose" placeholder="Home, Work..." :id="`purpose-${i}`" required />
                    <Input v-model="addr.address" label="Address" :id="`address-${i}`" required />
                    <Input v-model="addr.neighborhood" label="Neighborhood" :id="`neighborhood-${i}`" />
                    <Input v-model="addr.zip_code" label="ZIP Code" :id="`zip-${i}`" />
                    <Input v-model="addr.complement" label="Complement" :id="`complement-${i}`" />
                    <Input v-model="addr.reference" label="Reference" :id="`reference-${i}`" />
                    <Input v-model="addr.city_id" label="City ID" :id="`city-${i}`" />
                    <Input v-model="addr.state_id" label="State ID" :id="`state-${i}`" />
                    <Input v-model="addr.country_id" label="Country ID" :id="`country-${i}`" />
                    <Input v-model="addr.extra_info" label="Extra Info" :id="`extra-${i}`" />
                </div>
            </div>

            <p v-if="!form.list.length" class="py-6 text-zinc-400 text-sm text-center">No addresses added yet</p>

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

const props = defineProps({
    addresses: { type: Array, default: () => [] }
})

const route = useRoute()
const userStore = useUserStore()

const form = reactive({ list: [] })

watch(() => props.addresses, (val) => {
    form.list = (val ?? []).map(a => ({ ...a }))
}, { immediate: true })

function addAddress() {
    form.list.push({
        purpose: '',
        address: '',
        zip_code: '',
        city_id: '',
        state_id: '',
        country_id: '',
        neighborhood: '',
        complement: '',
        reference: '',
        extra_info: '',
    })
}

async function handleSubmit() {
    await userStore.updateAddresses(route.params.uuid, form.list)
}
</script>