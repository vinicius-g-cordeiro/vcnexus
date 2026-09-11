<template>
  <div class="bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
    <div class="mx-auto px-4 sm:px-6 py-10 max-w-5xl">
      <!-- Page header -->
      <div class="flex flex-col mb-8">
        <div class="flex flex-row justify-between">
          <h1 class="font-semibold text-2xl tracking-tight">{{ isView ? 'View User' : (isNew ? 'New User' : 'Edit User') }}</h1>
          <template v-if="!isNew">
            <span class="ms-auto me-0" v-if="isView">
              <Button variant="outline" :to="{ name: 'users.edit', params: { uuid: user.uuid } }" :title="t('users.list.results.actions.edit')"><i class="bi bi-pencil-square"></i> Edit {{ user.personal.name }}</Button>
            </span>
            <span class="ms-auto me-0" v-else>
              <Button variant="outline" :to="{ name: 'users.view', params: { uuid: user.uuid } }" :title="t('users.list.results.actions.edit')"><i class="bi bi-eye"></i> View {{ user.personal.name }}</Button>
            </span>
          </template>
        </div>
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

          <PermissionsSection :disabled="isView === true" v-show="activeSection === 'permissions' && canChangePermissions" v-model="user.permissions" />

          <RolesSection :disabled="isView === true" v-show="activeSection === 'roles' && canChangeRoles" v-model="user.roles" />
          
          <DocumentsSection :disabled="isView === true" v-show="activeSection === 'documents'" v-model="user.documents" />

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
import DocumentsSection from '@/components/users/DocumentsSection.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const userStore = useUserStore()



// --- mode -----------------------------------------------------------
// route param wins: presence of :id decides create vs edit.
const targetId = computed(() => route?.params?.uuid ?? null)
const isNew = computed(() => !targetId?.value)
const isView = computed(() => route.name === 'users.view' ?? null)


// --- access flags -----------------------------------------------
// In create mode: what can the CURRENT (logged-in) user grant?
// In edit mode: what does the TARGET user already have?
const isWorker = ref(false)
const canManageAccess = ref(false)
const canChangeRoles = ref(false)
const canChangePermissions = ref(false)

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
    religion: null,
    gender: null,
    sexual_orientation: null,
    marital_status: null,
    country: '',
    username: '',
  },
  documents: {
    taxpayer_id: '',
    national_id: '',
    national_id_issuer: '',
    drivers_license: '',
  },
  security: {
    password: '',
    password_confirmation: '',
  },
  business: {
    hourlyRate: '',
    hireDate: '',
    tenant: null,
    contractType: 1,
    contractFile: null,
  },
  bio: '',
  permissions: [],
  roles: [],
  tenant: null,
})

const user = reactive(emptyUser())
const avatarFile = ref(null)
const storageBase = import.meta.env.VITE_API_URL
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
  { key: 'documents', label: 'Documents'},
  { key: 'business', label: 'Business details', requires: 'worker' },
  { key: 'bio', label: 'Bio' },
  { key: 'permissions', label: 'Permissions', requires: 'permissions' },
  { key: 'roles', label: 'Roles', requires: 'roles' },
  { key: 'security', label: 'Security' },
]

const visibleSections = computed(() =>
  allSections.filter((s) => {
    if (s.requires === 'worker') return isWorker.value
    if (s.requires === 'access') return canManageAccess.value
    if (s.requires === 'roles') return canChangeRoles.value
    if (s.requires === 'permissions') return canChangePermissions.value
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
    avatarUrl: apiUser.avatar ?? '',
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
      birthdate: apiUser.birthdate ?? '',
      username: apiUser.username ?? '',
      country: apiUser.country ?? '',
    },
    documents: {
      taxpayer_id: apiUser.taxpayer_id ?? '',
      national_id: apiUser.national_id ?? '',
      national_id_issuer: apiUser.national_id_issuer ?? '',
      drivers_license: apiUser.drivers_license ?? '',
    },
    security: {
      password: '',
      password_confirmation: '',
    },
    business: {
      contractFile: apiUser.contract_file ?? null,
      contractType: apiUser.contract_type ?? 1,
      hourlyRate: apiUser.hourly_rate ?? '',
      hireDate: apiUser.hire_date ?? '',
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
    religion: user.personal.religion,
    avatar: user.avatar,
    marital_status: user.personal.marital_status,
    sexual_orientation: user.personal.sexual_orientation,
    locale: user.personal.locale,
    bio: user.bio,
    taxpayer_id: user.taxpayer_id,
    national_id: user.national_id,
    national_id_issuer: user.national_id_issuer,
    drivers_license: user.drivers_license,
    // Password: required on create, optional on edit (only sent if set).
    ...(isNew.value || user.security.password
      ? {
        password: user.security.password,
        password_confirmation: user.security.password_confirmation,
      }
      : {}),
    ...(isWorker.value
      ? {
        hourly_rate: user.business.hourlyRate,
        hire_date: user.business.hireDate,
        tenant: user.business.tenant,
        contract_file: user.business.contract_file,
        contract_type: user.business.contract_type,
      }
      : {}),
    ...(canChangePermissions.value
      ? {
        permissions: user.permissions,
      }
      : {}),
      ...(canChangeRoles.value ? 
      {
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
      isWorker.value = auth.sessionUser?.roles?.includes('2')
      canManageAccess.value = auth.isSuperAdmin
      canChangeRoles.value = auth.isSuperAdmin || auth.userPermissions.includes('users.roles')
      canChangePermissions.value = auth.isSuperAdmin || auth.userPermissions.includes('users.permissions')
    } else {
      const ok = await userStore.fetchUser(targetId.value)
      Object.assign(user, mapApiUserToForm(userStore.user))
      isWorker.value = auth.sessionUser?.roles?.includes('3')
      canManageAccess.value = auth.isSuperAdmin
      canChangeRoles.value = auth.isSuperAdmin || auth.userPermissions.includes('users.roles')
      canChangePermissions.value = auth.isSuperAdmin || auth.userPermissions.includes('users.permissions')
    }
  } catch (err) {    
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

    if (avatarFile.value && saved.ok == true) {
      const formData = new FormData()
      formData.append('avatar', avatarFile.value)
      await userStore.updateUserAvatar(saved.user?.uuid, formData)
    }

    saveSuccess.value = true
    user.security.password = ''
    user.security.password_confirmation = ''
    avatarFile.value = null

    if (isNew.value) {
      // Move to the edit route for the newly created user so a
      // refresh or further edits target the right record.
      router.replace({ name: 'users.list' })
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
    router.push({ name: 'users.list' })
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