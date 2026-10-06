<template>
    <div class="flex md:flex-row flex-col gap-4 bg-zinc-100 dark:bg-zinc-950 min-h-screen">
        <aside class="flex-shrink-0 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 md:border-r w-full md:w-64">
            <div class="p-4 border-zinc-200 dark:border-zinc-800 border-b">
                <h1 class="font-semibold text-zinc-900 dark:text-zinc-100 text-base">New User</h1>
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
                    <i v-if="section.required && !sectionComplete[section.name]" class="ms-auto text-amber-500 text-xl bi bi-dot"></i>
                </button>
            </nav>
        </aside>

        <main class="flex-1 p-4 sm:p-6">
            <div v-if="submitError" class="flex items-start gap-2 bg-red-50 dark:bg-red-950/40 mb-4 p-3 border border-red-200 dark:border-red-900 rounded-lg max-w-2xl text-red-700 dark:text-red-400 text-sm">
                <i class="mt-0.5 bi bi-exclamation-triangle"></i>
                <span>{{ submitError }}</span>
            </div>

            <UserProfile v-show="selectedSection === 'profile'" mode="create" :profile="draft.profile" @update:modelValue="draft.profile = $event" />
            <UserCredentials v-show="selectedSection === 'credentials'" mode="create" :credentials="draft.credentials" @update:modelValue="draft.credentials = $event" />
            <UserPermissions v-show="selectedSection === 'permissions'" mode="create" :permissions="permissionsForDisplay" @update:modelValue="draft.permissions = $event" />
            <UserRoles v-show="selectedSection === 'roles'" mode="create" :roles="availableRoles" @update:modelValue="draft.roles = $event" />
            <UserAddresses v-show="selectedSection === 'addresses'" mode="create" :addresses="draft.addresses" @update:modelValue="draft.addresses = $event" />
            <UserContacts v-show="selectedSection === 'contacts'" mode="create" :contacts="draft.contacts" @update:modelValue="draft.contacts = $event" />
            <UserConsents v-show="selectedSection === 'consents'" mode="create" :consents="draft.consents" @update:modelValue="draft.consents = $event" />
            <UserSensitive v-show="selectedSection === 'sensitive'" mode="create" :sensitive="draft.sensitive" @update:modelValue="draft.sensitive = $event" />

            <div class="flex items-center gap-3 mt-6 max-w-2xl">
                <button type="button" :disabled="submitting" @click="handleCreate" class="flex items-center gap-2 bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 disabled:opacity-50 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    <i v-if="submitting" class="animate-spin bi bi-arrow-repeat"></i>
                    {{ submitting ? 'Creating...' : 'Create User' }}
                </button>
                <span class="text-zinc-500 dark:text-zinc-400 text-xs">All sections are saved together when you create the user.</span>
            </div>
        </main>
    </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import { useAuthorizationStore } from '@/stores/authorization/authorizationStore'
import UserProfile from '@/components/forms/users/UserProfile.vue'
import UserCredentials from '@/components/forms/users/UserCredentials.vue'
import UserPermissions from '@/components/forms/users/UserPermissions.vue'
import UserRoles from '@/components/forms/users/UserRoles.vue'
import UserAddresses from '@/components/forms/users/UserAddresses.vue'
import UserContacts from '@/components/forms/users/UserContacts.vue'
import UserConsents from '@/components/forms/users/UserConsents.vue'
import UserSensitive from '@/components/forms/users/UserSensitive.vue'

const router = useRouter()
const userStore = useUserStore()
const authorizationStore = useAuthorizationStore()

const sections = ref([
    { name: 'profile', label: 'Profile', icon: 'bi-person', required: true },
    { name: 'credentials', label: 'Credentials', icon: 'bi-key', required: true },
    { name: 'permissions', label: 'Permissions', icon: 'bi-shield-check', required: false },
    { name: 'roles', label: 'Roles', icon: 'bi-person-badge', required: false },
    { name: 'addresses', label: 'Addresses', icon: 'bi-geo-alt', required: false },
    { name: 'contacts', label: 'Contacts', icon: 'bi-telephone', required: false },
    { name: 'consents', label: 'Consents', icon: 'bi-file-earmark-check', required: false },
    { name: 'sensitive', label: 'Sensitive', icon: 'bi-eye-slash', required: false },
])

const selectedSection = ref('profile')
const submitting = ref(false)
const submitError = ref('')
const availablePermissions = ref([])
const availableRoles = ref([])

// draft holds every section's local state until the final submit fires
const draft = reactive({
    profile: {},
    credentials: {},
    permissions: [],
    roles: [],
    addresses: [],
    contacts: [],
    consents: [],
    sensitive: {},
})

const sectionComplete = computed(() => ({
    profile: !!(draft.profile.firstname && draft.profile.lastname),
    credentials: !!(draft.credentials.email && draft.credentials.password),
    roles: draft.roles.length > 0,
    permissions: draft.permissions.length > 0,
}))

// merges the static catalog with whatever's already checked in draft.permissions,
// so re-visiting the tab shows previous selections instead of resetting to unchecked.
// matched by id, not slug — slug grouping is still used for display inside
// UserPermissions, but identity/selection state is tracked by id throughout
const permissionsForDisplay = computed(() => {
    const grantedIds = new Set(draft.permissions.map(p => p.id))
    return availablePermissions.value.map(p => ({ ...p, granted: grantedIds.has(p.id) }))
})

onMounted(async () => {
    // both permissions and roles come from the authorization module/store now
    await authorizationStore.fetchPermissions()
    availablePermissions.value = authorizationStore.permissions ?? []

    await authorizationStore.fetchRoles()
    availableRoles.value = authorizationStore.roles ?? []
})

// Recursively appends a value into FormData using PHP's bracket-array
// convention, e.g. appendToFormData(fd, 'addresses', [{purpose:'Home'}])
// produces the key "addresses[0][purpose]" with value "Home". This is what
// lets a nested array of objects survive a multipart/form-data request and
// arrive on the PHP side as a proper indexed array of associative arrays —
// FormData.append('addresses[]', obj) does NOT do this, it just stringifies
// the object into the literal text "[object Object]".
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
            // avatarFile is carried inside draft.profile for the local preview/upload
            // flow, but it's sent separately as a top-level "avatar" field below —
            // skip it here so it isn't also serialized as profile[avatarFile]
            if (childKey === 'avatarFile') return
            appendToFormData(formData, `${key}[${childKey}]`, childValue)
        })
        return
    }
    formData.append(key, value)
}

async function handleCreate() {
    submitError.value = ''

    if (!sectionComplete.value.profile) {
        submitError.value = 'First name and last name are required.'
        selectedSection.value = 'profile'
        return
    }
    if (!sectionComplete.value.credentials) {
        submitError.value = 'Email and password are required.'
        selectedSection.value = 'credentials'
        return
    }
    if (!sectionComplete.value.roles) {
        submitError.value = 'At least one role is required.'
        selectedSection.value = 'roles'
        return
    }
    if (!sectionComplete.value.permissions) {
        submitError.value = 'At least one permission is required.'
        selectedSection.value = 'permissions'
        return
    }

    submitting.value = true
    try {
        // one combined payload — createUser takes the whole thing in a single call,
        // backend wraps the full insert (credentials, profile, roles, permissions,
        // addresses, contacts, consents, sensitive) in one transaction
        const payload = new FormData()

        appendToFormData(payload, 'credentials', draft.credentials)
        appendToFormData(payload, 'profile', draft.profile)

        // roles/permissions are references into existing catalog rows, so only
        // the ids need to travel — appendToFormData flattens this into
        // roles[0], roles[1], ... which PHP collects as a plain indexed array
        appendToFormData(payload, 'roles', draft.roles.map(r => r.id))
        appendToFormData(payload, 'permissions', draft.permissions.map(p => p.id))

        // these are full records being created, not references, so the entire
        // object per row needs to survive — appendToFormData turns each into
        // addresses[0][purpose], addresses[0][address], addresses[1][purpose], ...
        appendToFormData(payload, 'addresses', draft.addresses)
        appendToFormData(payload, 'contacts', draft.contacts)
        appendToFormData(payload, 'consents', draft.consents)
        appendToFormData(payload, 'sensitive', draft.sensitive)

        if (draft.profile.avatarFile) payload.append('avatar', draft.profile.avatarFile)

        const { uuid } = await userStore.createUser(payload)
        router.push({ name: 'users.edit', params: { uuid } })
    } catch (err) {
        submitError.value = err?.response?.data?.message ?? 'Failed to create user. Please check the required fields and try again.'
    } finally {
        submitting.value = false
    }
}
</script>