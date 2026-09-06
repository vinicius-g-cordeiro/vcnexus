<template>
  <div class="bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
    <div class="mx-auto px-4 sm:px-6 py-10 max-w-5xl">
      <!-- Page header -->
      <div class="mb-8">
        <h1 class="font-semibold text-2xl tracking-tight">{{ isView ? 'View User' : (isNew ? 'New User' : 'Edit User') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400 text-sm">
          {{ isView ? 'View this profile' : (isNew ? 'Create a new profile' : 'Update this profile') }}
          <i class="bi" :class="isNew ? 'bi-person-add' : 'bi-pencil-square'"></i>
        </p>
      </div>

      <!-- Loading state -->
      <div v-if="isLoading" class="flex justify-center items-center py-24">
        <svg class="w-6 h-6 text-neutral-500 dark:text-neutral-400 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
      </div>

      <!-- Load error -->
      <div v-else-if="loadError" class="bg-red-500/10 px-4 py-3 border border-red-500/30 rounded-lg text-red-500 text-sm">
        {{ loadError }}
        <button type="button" class="ml-2 font-medium underline underline-offset-2" @click="loadUser">
          Try again
        </button>
      </div>

      <div v-else class="flex lg:flex-row flex-col gap-8">
        <!-- Section nav -->
        <div class="lg:w-56 shrink-0">
          <SettingsNav v-model="activeSection" :sections="visibleSections" />
        </div>

        <!-- Active section -->
        <div class="flex flex-col flex-1 gap-6 min-w-0">
          <AvatarUpload :disabled="isView === true" v-show="activeSection === 'avatar'" v-model="user.avatarUrl" :name="fullName" @update:file="handleAvatarFile" />

          <PersonalDetailsSection :disabled="isView === true" v-show="activeSection === 'personal'" v-model="user.personal" :errors="errors.personal" />

          <SecuritySection :disabled="isView === true" v-show="activeSection === 'security'" v-model="user.security" :errors="errors.security" :password-required="isNew" />

          <BusinessDetailsSection :disabled="isView === true" v-show="activeSection === 'business' && isWorker" v-model="user.business" :errors="errors.business" />

          <BioSection :disabled="isView === true" v-show="activeSection === 'bio'" v-model="user.bio" :error="errors.bio" />

          <PermissionsSection :disabled="isView === true" v-show="activeSection === 'permissions' && canManageAccess" v-model="user.permissions" />

          <RolesSection :disabled="isView === true" v-show="activeSection === 'roles' && canManageAccess" v-model="user.roles" />
          <template v-if="isView === false">
            <p v-if="saveError" class="text-red-500 text-sm">{{ saveError }}</p>
            <p v-if="saveSuccess" class="text-emerald-500 text-sm">
              {{ isNew ? 'User created.' : 'Changes saved.' }}
            </p>

            <!-- Save bar -->
            <div class="flex justify-end items-center gap-3 pt-2">
              <Button variant="ghost" :disabled="isSaving" @click="handleCancel">
                Cancel
              </Button>
              <Button :loading="isSaving" @click="handleSave">
                {{ isNew ? 'Create user' : 'Save' }}
              </Button>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * UserForm.vue — create/edit page for a user profile.
 *
 * MODE
 *   Driven by the `:id` route param.
 *     - No `id`   -> "create" mode: blank form, POSTs a new user.
 *     - `id` set  -> "edit" mode: loads that user by id, PUTs updates.
 *
 *   This is NOT the "edit my own account" page. It edits an
 *   arbitrary user by id (an admin-style page) and is backed by
 *   `userStore`, not `authStore`. If you need a "my profile" page
 *   for the logged-in user, that should be a separate component
 *   backed by `authStore.sessionUser` / `authStore.updateUser`, to
 *   avoid conflating "who's logged in" with "who's being edited".
 *
 * PERMISSIONS
 *   `isWorker` and `canManageAccess` gate the Business/Permissions/
 *   Roles tabs. In create mode these reflect what the CREATOR (the
 *   logged-in admin) is allowed to assign, since there's no target
 *   user yet to derive them from. In edit mode they reflect the
 *   loaded target user's own record.
 *
 * PROPS / ROUTING
 *   Expected route config, e.g.:
 *     { path: 'users/new', name: 'users.new', component: UserForm }
 *     { path: 'users/:id/edit', name: 'users.edit', component: UserForm }
 */
import { reactive, ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { useUserStore } from '@/stores/userStore'

import SettingsNav from '@/components/SettingsNav.vue'
import AvatarUpload from '@/components/users/AvatarUpload.vue'
import PersonalDetailsSection from '@/components/users/PersonalDetailsSection.vue'
import BusinessDetailsSection from '@/components/users/BusinessDetailsSection.vue'
import BioSection from '@/components/users/BioSection.vue'
import PermissionsSection from '@/components/users/PermissionsSection.vue'
import RolesSection from '@/components/users/RolesSection.vue'
import Button from '@/components/Button.vue'
import SecuritySection from '@/components/users/SecuritySection.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const userStore = useUserStore()



// --- mode -----------------------------------------------------------
// route param wins: presence of :id decides create vs edit.
const targetId = computed(() => route.params.uuid ?? null)
const isNew = computed(() => !targetId.value)
const isView = computed(() => route.name === 'users-view' ?? null)


// --- access flags -----------------------------------------------
// In create mode: what can the CURRENT (logged-in) user grant?
// In edit mode: what does the TARGET user already have?
const isWorker = ref(true)
const canManageAccess = ref(true)

// --- local state ---------------------------------------------------
const emptyUser = () => ({
  id: null,
  avatarUrl: '',
  personal: {
    name: '',
    lastname: '',
    email: '',
    phone: '',
    birthDate: '',
    country: '',
    username: '',
  },
  security: {
    password: '',
    password_confirmation: '',
  },
  business: {
    companyName: '',
    taxId: '',
    jobTitle: '',
    department: '',
    hourlyRate: '',
    hireDate: '',
    isContractor: false,
    tenant: null,
  },
  bio: '',
  permissions: [],
  roles: [],
  tenant: null,
})

const user = reactive(emptyUser())
const avatarFile = ref(null)

const isLoading = ref(true)
const loadError = ref('')
const isSaving = ref(false)
const saveError = ref('')
const saveSuccess = ref(false)
const errors = reactive({ personal: {}, business: {}, bio: '', security: {}, roles: {}, permissions: {} })

// --- sections -----------------------------------------------------
const allSections = [
  { key: 'avatar', label: 'Avatar' },
  { key: 'personal', label: 'Personal details' },
  { key: 'business', label: 'Business details', requires: 'worker' },
  { key: 'bio', label: 'Bio' },
  { key: 'permissions', label: 'Permissions', requires: 'access' },
  { key: 'roles', label: 'Roles', requires: 'access' },
  { key: 'security', label: 'Security' },
]

const visibleSections = computed(() =>
  allSections.filter((s) => {
    if (s.requires === 'worker') return isWorker.value
    if (s.requires === 'access') return canManageAccess.value
    return true
  })
)

const activeSection = ref('avatar')

const fullName = computed(() =>
  [user.personal.name, user.personal.lastname].filter(Boolean).join(' ')
)

// --- mapping: raw API payload -> the shape the form sections expect --
// Adjust the right-hand-side keys to whatever your backend actually
// calls them; this is the ONE place that needs to change if your
// API's field names differ from what's listed here.
function mapApiUserToForm(apiUser) {
  return {
    id: apiUser.id,
    avatarUrl: apiUser.avatar_url ?? apiUser.avatarUrl ?? '',
    personal: {
      name: apiUser.name ?? '',
      lastname: apiUser.lastname ?? '',
      surname: apiUser.surname ?? '',
      religion: apiUser.religion ?? '',
      sexual_orientation: apiUser.sexual_orientation ?? '',
      marital_status: apiUser.marital_status ?? '',
      gender: apiUser.gender ?? '',
      email: apiUser.email ?? '',
      phone: apiUser.phone ?? '',
      locale: apiUser.locale ?? '',
      birthDate: apiUser.birthdate ?? '',
      username: apiUser.username ?? '',
      country: apiUser.country ?? '',
    },
    security: {
      password: '',
      password_confirmation: '',
    },
    business: {
      companyName: apiUser.company_name ?? '',
      taxId: apiUser.tax_id ?? '',
      jobTitle: apiUser.job_title ?? '',
      department: apiUser.department ?? '',
      hourlyRate: apiUser.hourly_rate ?? '',
      hireDate: apiUser.hire_date ?? '',
      isContractor: apiUser.is_contractor ?? false,
      tenant: apiUser.tenant ?? null,
    },
    bio: apiUser.bio ?? '',
    permissions: apiUser.permissions ?? [],
    roles: apiUser.roles ?? [],
    tenant: apiUser.tenant ?? null,
  }
}

// --- mapping: form state -> API payload ------------------------------
function mapFormToApiPayload() {
  return {
    name: user.personal.name,
    lastname: user.personal.lastname,
    username: user.personal.username,
    surname: user.personal.surname,
    email: user.personal.email,
    phone: user.personal.phone,
    birthdate: user.personal.birthdate,
    country: user.personal.country,
    gender: user.personal.gender,
    marital_status: user.personal.marital_status,
    sexual_orientation: user.personal.sexual_orientation,
    locale: user.personal.locale,
    bio: user.bio,
    // Password: required on create, optional on edit (only sent if set).
    ...(isNew.value || user.security.password
      ? {
        password: user.security.password,
        password_confirmation: user.security.password_confirmation,
      }
      : {}),
    ...(isWorker.value
      ? {
        company_name: user.business.companyName,
        tax_id: user.business.taxId,
        job_title: user.business.jobTitle,
        department: user.business.department,
        hourly_rate: user.business.hourlyRate,
        hire_date: user.business.hireDate,
        is_contractor: user.business.isContractor,
        tenant: user.business.tenant,
      }
      : {}),
    ...(canManageAccess.value
      ? {
        permissions: user.permissions,
        roles: user.roles,
      }
      : {}),
  }
}

// --- data loading ---------------------------------------------------
async function loadUser() {
  isLoading.value = true
  loadError.value = ''
  try {
    if (isNew.value) {
      // Nothing to fetch: start from a blank record. Permission
      // flags reflect what the logged-in admin is allowed to grant.
      Object.assign(user, emptyUser())
      isWorker.value = true
      canManageAccess.value = auth.sessionUser?.role === '1'
    } else {
      const ok = await userStore.fetchUser(targetId.value)
      Object.assign(user, mapApiUserToForm(userStore.user))
      isWorker.value = auth.sessionUser?.role === '1'
      canManageAccess.value = auth.sessionUser?.role === '1'
    }
  } catch (err) {
    console.error(err)
    loadError.value = isNew.value
      ? 'Could not prepare the form. Please try again.'
      : 'Could not load this user. Please try again.'
  } finally {
    isLoading.value = false
  }
}

// --- save -----------------------------------------------------------
async function handleSave() {
  if (isView === true) {
    return
  }
  isSaving.value = true
  saveError.value = ''
  saveSuccess.value = false
  try {

    const payload = mapFormToApiPayload()

    const saved = isNew.value
      ? await userStore.createUser(payload)
      : await userStore.updateUser(targetId.value, payload)

    console.log(saved)

    if (avatarFile.value) {
      const formData = new FormData()
      formData.append('avatar', avatarFile.value)
      await userStore.updateUserAvatar(saved.id, formData)
    }

    saveSuccess.value = true
    user.security.password = ''
    user.security.password_confirmation = ''
    avatarFile.value = null

    if (isNew.value) {
      // Move to the edit route for the newly created user so a
      // refresh or further edits target the right record.
      router.replace({ name: 'users.edit', params: { id: saved.id } })
    }
  } catch (err) {
    const fieldErrors = err?.response?.data?.errors
    if (fieldErrors) {
      Object.assign(errors, fieldErrors)
    }
    saveError.value = err?.response?.data?.message
      ?? (isNew.value ? 'Could not create user. Please try again.' : 'Could not save changes. Please try again.')
  } finally {
    isSaving.value = false
  }
}

function handleCancel() {
  if (isView === true) {
    return
  }
  if (isNew.value) {
    router.push({ name: 'users.index' })
    return
  }
  loadUser()
  avatarFile.value = null
  saveError.value = ''
  saveSuccess.value = false
}

function handleAvatarFile(file) {
  if (isView === true) {
    return
  }
  avatarFile.value = file
}

onMounted(loadUser)
</script>