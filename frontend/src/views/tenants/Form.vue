<template>
    <div class="flex md:flex-row flex-col gap-4 bg-zinc-100 dark:bg-zinc-950 min-h-screen">
        <aside class="flex-shrink-0 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 md:border-r w-full md:w-64">
            <div class="p-4 border-zinc-200 dark:border-zinc-800 border-b">
                <h1 class="font-semibold text-zinc-900 dark:text-zinc-100 text-base">New Tenant</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs">Fill in and save once</p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <button v-for="section in sections" :key="section.name" type="button" @click="selectedSection = section.name" :class="[
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-left transition-colors',
                    selectedSection === section.name
                        ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900'
                        : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'
                ]">
                    <i :class="['bi', section.icon, 'text-base']"></i>
                    {{ section.label }}

                </button>
            </nav>
        </aside>

        <main class="flex-1 p-4 sm:p-6">
            <div v-if="submitError" class="flex items-start gap-2 bg-red-50 dark:bg-red-950/40 mb-4 p-3 border border-red-200 dark:border-red-900 rounded-lg max-w-2xl text-red-700 dark:text-red-400 text-sm">
                <i class="mt-0.5 bi bi-exclamation-triangle"></i>
                <span>{{ submitError }}</span>
            </div>
            <TenantOrganization v-show="selectedSection === 'organization'" mode="create" :fields="organization" @update:modelValue="organization = $event" />
            <TenantAddresses v-show="selectedSection === 'addresses'" mode="create" :fields="addresses" @update:modelValue="addresses = $event" />
            <TenantContacts v-show="selectedSection === 'contacts'" mode="create" :contacts="contacts" @update:modelValue="contacts = $event" />
            <TenantBranding v-show="selectedSection === 'brandings'" mode="create" :branding="brandings" @update:modelValue="brandings = $event" />

            <div class="flex items-center gap-3 mt-6 max-w-2xl">
                <button type="button" :disabled="submitting" @click="handleCreate" class="flex items-center gap-2 bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 disabled:opacity-50 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    <i v-if="submitting" class="animate-spin bi bi-arrow-repeat"></i>
                    {{ submitting ? 'Creating...' : 'Create Tenant' }}
                </button>
                <span class="text-zinc-500 dark:text-zinc-400 text-xs">All sections are saved together when you create the tenant.</span>
            </div>
        </main>
    </div>

</template>

<script setup="js">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTenantStore } from '@/stores/tenants/tenantStore'
import TenantAddresses from '@/components/forms/tenants/TenantAddresses.vue'
import TenantContacts from '@/components/forms/tenants/TenantContacts.vue'
import TenantOrganization from '@/components/forms/tenants/TenantOrganization.vue'
import TenantBranding from '@/components/forms/tenants/TenantBranding.vue'


const route = useRoute()
const router = useRouter()
const submitting = ref(false)

const tenantStore = useTenantStore()
const sections = ref([
    { name: 'organization', label: 'Organization', icon: 'bi-building', required: true },
    { name: 'brandings', label: 'Brandings', icon: 'bi-images', required: true },
    { name: 'addresses', label: 'Addresses', icon: 'bi-geo-alt', required: false },
    { name: 'contacts', label: 'Contacts', icon: 'bi-telephone', required: false },
]);

const selectedSection = ref('organization')


const organization =  computed(() => tenantStore.organization?.list ??[])
const brandings =  computed(() => tenantStore.brandings?.list ??[])
const addresses =  computed(() => tenantStore.addresses ?? [])
const contacts =  computed(() => tenantStore.contacts ?? [])


const submitError = ref('')


function appendToFormData(formData, key, value) {
    if (value === null || value === undefined) {
        return
    }
    if (value instanceof File || value instanceof Blob) {
        formData.append(key, value)
        return
    }
    if (Array.isArray(value)) {
        value.forEach((item, index) => appendToFormData(formData, `${key}[${index}]`, item))
        return
    }
    if (typeof value === 'object') {
        Object.entries(value).forEach(([childKey, childValue]) => {
            if (childKey === 'logo') return
            appendToFormData(formData, `${key}[${childKey}]`, childValue)
        })
        return
    }
    formData.append(key, value)
}

async function handleCreate() {
    try{
        const payload = new FormData();
        // change the playload to be a post object

        appendToFormData(payload, 'organization', organization)
        appendToFormData(payload, 'brandings', brandings)
        appendToFormData(payload, 'addresses', addresses)
        appendToFormData(payload, 'contacts', contacts)

        if (brandings.logo) payload.append('logo', brandings.logo)

        const { uuid } = await tenantStore?.createTenant(payload)

        router.push({ name: 'tenants.edit', params: { uuid } })
    }catch (err) {
        submitError.value = err?.response?.data?.message ?? 'Failed to create tenant/organization. Please check the required fields and try again.'
    } finally {
        submitting.value = false
    }
}

onMounted(async() => {
    await tenantStore.fetchOrganization(route.params.uuid)
    await tenantStore.fetchBrandings(route.params.uuid)
    await tenantStore.fetchAddresses(route.params.uuid)
    await tenantStore.fetchContacts(route.params.uuid)
});

</script>
