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
            <div v-for="(addr, i) in form.list" :key="addr._key" class="relative bg-zinc-50 dark:bg-zinc-800/40 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <button type="button" @click="removeAddress(i)" class="top-3 right-3 absolute text-zinc-400 hover:text-red-500 transition-colors">
                    <i class="bi bi-trash"></i>
                </button>
                <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                    <Select label="Country" :id="`country-${addr._key}`" :options="countryOptions" v-model="addr.country_id" @change="onCountryChange(addr)" placeholder="Select country" />
                    <Select label="State" :id="`state-${addr._key}`" :options="addr._stateOptions" v-model="addr.state_id" @change="onStateChange(addr)" placeholder="Select state" :disabled="!addr.country_id || addr._loadingStates" />
                    <Select label="City" :id="`city-${addr._key}`" :options="addr._cityOptions" v-model="addr.city_id" placeholder="Select city" :disabled="!addr.state_id || addr._loadingCities" />
                    <Input v-model="addr.purpose" label="Purpose" placeholder="Home, Work..." :id="`purpose-${addr._key}`" required />
                    <Input v-model="addr.address" label="Address" :id="`address-${addr._key}`" required />
                    <Input v-model="addr.neighborhood" label="Neighborhood" :id="`neighborhood-${addr._key}`" />
                    <Input v-model="addr.zip_code" label="ZIP Code" :id="`zip-${addr._key}`" />
                    <Input v-model="addr.complement" label="Complement" :id="`complement-${addr._key}`" />
                    <Input v-model="addr.reference" label="Reference" :id="`reference-${addr._key}`" />
                    <Input v-model="addr.extra_info" label="Extra Info" :id="`extra-${addr._key}`" />
                </div>
            </div>

            <p v-if="!form.list.length" class="py-6 text-zinc-400 text-sm text-center">No addresses added yet</p>

            <div v-if="mode === 'edit'" class="flex justify-end pt-2">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, watch, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import { useReferencesStore } from '@/stores/references/referencesStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    addresses: { type: Array, default: () => [] },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()
const referencesStore = useReferencesStore()

const countryOptions = ref([])

const statesCache = new Map()
const citiesCache = new Map()

const form = reactive({ list: [] })

let nextKey = 0
function makeRowKey() {
    return nextKey++
}

function toRow(raw) {
    return {
        ...raw,
        complement: raw.complement ?? '',
        reference: raw.reference ?? '',
        extra_info: raw.extra_info ?? '',
        _key: makeRowKey(),
        _stateOptions: [],
        _cityOptions: [],
        _loadingStates: false,
        _loadingCities: false,
    }
}

function toPayload(row) {
    const { _key, _stateOptions, _cityOptions, _loadingStates, _loadingCities, ...rest } = row
    return rest
}

if (props.mode === 'create') {
    form.list = (props.addresses ?? []).map(toRow)
    watch(form, (val) => emit('update:modelValue', val.list.map(toPayload)), { deep: true })
} else {
    watch(() => props.addresses, (val) => {
        form.list = (val ?? []).map(toRow)
    }, { immediate: true })
}

function addAddress() {
    form.list.push(toRow({
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
    }))
}

function removeAddress(index) {
    form.list.splice(index, 1)
}

async function handleSubmit() {
    if (props.mode !== 'edit') return
    await userStore.updateAddresses(route.params.uuid, form.list.map(toPayload))
}

onMounted(async () => {
    const ok = await referencesStore.fetchCountries()
    if (ok !== true) return
    countryOptions.value = referencesStore.countries.map(c => ({ label: c.name, value: c.id }))

    if (props.mode === 'edit') {
        await Promise.all(
            form.list.map(async (row) => {
                if (row.country_id) await loadStatesForRow(row, row.country_id)
                if (row.state_id) await loadCitiesForRow(row, row.state_id, row.country_id)
            })
        )
    }
})

async function loadStatesForRow(row, countryId) {
    if (!countryId) {
        row._stateOptions = []
        row._cityOptions = []
        return
    }
    if (statesCache.has(countryId)) {
        row._stateOptions = statesCache.get(countryId)
        return
    }
    row._loadingStates = true
    try {
        const ok = await referencesStore.fetchStates({}, countryId)
        if (ok === true) {
            const options = referencesStore.states.map(s => ({ label: s.name, value: s.id }))
            statesCache.set(countryId, options)
            row._stateOptions = options
        }
    } finally {
        row._loadingStates = false
    }
}

async function loadCitiesForRow(row, stateId, countryId) {
    if (!stateId) {
        row._cityOptions = []
        return
    }
    const cacheKey = `${stateId}:${countryId}`
    if (citiesCache.has(cacheKey)) {
        row._cityOptions = citiesCache.get(cacheKey)
        return
    }
    row._loadingCities = true
    try {
        const ok = await referencesStore.fetchCities({}, stateId, countryId)
        if (ok === true) {
            const options = referencesStore.cities.map(c => ({ label: c.name, value: c.id }))
            citiesCache.set(cacheKey, options)
            row._cityOptions = options
        }
    } finally {
        row._loadingCities = false
    }
}


async function onCountryChange(addr) {
    addr.state_id = ''
    addr.city_id = ''
    addr._stateOptions = []
    addr._cityOptions = []
    await loadStatesForRow(addr, addr.country_id)
}

async function onStateChange(addr) {
    addr.city_id = ''
    addr._cityOptions = []
    await loadCitiesForRow(addr, addr.state_id, addr.country_id)
}
</script>