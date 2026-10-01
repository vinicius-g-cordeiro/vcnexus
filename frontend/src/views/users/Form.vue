<template>
    <div class="flex md:flex-row flex-col gap-4 bg-zinc-100 dark:bg-zinc-950 min-h-screen">
        <aside class="flex-shrink-0 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 md:border-r w-full md:w-64">
            <div class="p-4 border-zinc-200 dark:border-zinc-800 border-b">
                <h1 class="font-semibold text-zinc-900 dark:text-zinc-100 text-base">User Settings</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs">Manage account details</p>
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
            <UserProfile v-if="selectedSection === 'profile'" :profile="profile" />
            <UserCredentials v-if="selectedSection === 'credentials'" :credentials="credentials" />
            <UserPermissions v-if="selectedSection === 'permissions'" :permissions="permissions" />
            <UserAddresses v-if="selectedSection === 'addresses'" :addresses="addresses" />
            <UserContacts v-if="selectedSection === 'contacts'" :contacts="contacts" />
            <UserConsents v-if="selectedSection === 'consents'" :consents="consents" />
            <UserSensitive v-if="selectedSection === 'sensitive'" :sensitive="sensitive" />
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { useUserStore } from '@/stores/users/userStore'
import UserProfile from '@/components/forms/users/UserProfile.vue'
import UserCredentials from '@/components/forms/users/UserCredentials.vue'
import UserPermissions from '@/components/forms/users/UserPermissions.vue'
import UserAddresses from '@/components/forms/users/UserAddresses.vue'
import UserContacts from '@/components/forms/users/UserContacts.vue'
import UserConsents from '@/components/forms/users/UserConsents.vue'
import UserSensitive from '@/components/forms/users/UserSensitive.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()

const sections = ref([
    { name: 'profile', label: 'Profile', icon: 'bi-person' },
    { name: 'credentials', label: 'Credentials', icon: 'bi-key' },
    { name: 'permissions', label: 'Permissions', icon: 'bi-shield-check' },
    { name: 'addresses', label: 'Addresses', icon: 'bi-geo-alt' },
    { name: 'contacts', label: 'Contacts', icon: 'bi-telephone' },
    { name: 'consents', label: 'Consents', icon: 'bi-file-earmark-check' },
    { name: 'sensitive', label: 'Sensitive', icon: 'bi-eye-slash' },
])

const selectedSection = ref('profile')

const profile = computed(() => userStore.profile)
const credentials = computed(() => userStore.credentials)
const permissions = computed(() => userStore.permissions)
const addresses = computed(() => userStore.addresses)
const contacts = computed(() => userStore.contacts)
const consents = computed(() => userStore.consents)
const sensitive = computed(() => userStore.sensitive)

onMounted(async () => {
    await userStore.fetchProfile(route.params.uuid)
    await userStore.fetchCredentials(route.params.uuid)
    await userStore.fetchPermissions(route.params.uuid)
    await userStore.fetchAddresses(route.params.uuid)
    await userStore.fetchContacts(route.params.uuid)
    await userStore.fetchConsents(route.params.uuid)
    await userStore.fetchSensitive(route.params.uuid)
})
</script>