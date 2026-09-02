<template>
  <div class="bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
    <div class="mx-auto px-4 sm:px-6 py-10 max-w-5xl">
      <!-- Page header -->
      <div class="mb-8">
        <h1 class="font-semibold text-2xl tracking-tight">Profile settings</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400 text-sm">
          Manage {{ isOwnProfile ? 'your' : `${user.personal.firstName}'s` }} profile, access and role information.
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
        <button type="button" class="ml-2 font-medium underline underline-offset-2" @click="fetchUser">
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
          <AvatarUpload
            v-show="activeSection === 'avatar'"
            v-model="user.avatarUrl"
            :name="fullName"
            @update:file="handleAvatarFile"
          />

          <PersonalDetailsSection
            v-show="activeSection === 'personal'"
            v-model="user.personal"
            :errors="errors.personal"
          />

          <SecuritySection
            v-show="activeSection === 'security'"
            v-model="user.security"
            :errors="errors.security"
          />

          <BusinessDetailsSection
            v-show="activeSection === 'business' && isWorker"
            v-model="user.business"
            :errors="errors.business"
          />

          <BioSection
            v-show="activeSection === 'bio'"
            v-model="user.bio"
            :error="errors.bio"
          />

          <PermissionsSection
            v-show="activeSection === 'permissions' && canManageAccess"
            v-model="user.permissions"
          />

          <RolesSection
            v-show="activeSection === 'roles' && canManageAccess"
            v-model="user.roles"
          />

          <p v-if="saveError" class="text-red-500 text-sm">{{ saveError }}</p>
          <p v-if="saveSuccess" class="text-emerald-500 text-sm">Changes saved.</p>

          <!-- Save bar -->
          <div class="flex justify-end items-center gap-3 pt-2">
            <AppButton variant="ghost" :disabled="isSaving" @click="handleCancel">
              Cancel
            </AppButton>
            <AppButton :loading="isSaving" @click="handleSave">
              Save changes
            </AppButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * Profile.vue — user profile settings page, routed to directly
 * (e.g. `path: 'profile/'`) rather than mounted by a parent. It
 * loads its own data and performs its own save — no props required
 * to use it from the router.
 *
 * DATA SOURCE
 *   This page edits the CURRENT SESSION USER, sourced from the auth
 *   store (`auth.fetchUser()` / `auth.user`) rather than a route
 *   param. See the README for the exact API response shape expected.
 *
 * PERMISSIONS
 *   `isWorker` and `canManageAccess` gate the Business/Permissions/
 *   Roles/Security tabs. Derived from `auth.user` once loaded.
 */
import { reactive, ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/authStore'

import SettingsNav from '@/components/SettingsNav.vue'
import AvatarUpload from '@/components/AvatarUpload.vue'
import PersonalDetailsSection from '@/components/PersonalDetailsSection.vue'
import BusinessDetailsSection from '@/components/BusinessDetailsSection.vue'
import BioSection from '@/components/BioSection.vue'
import PermissionsSection from '@/components/PermissionsSection.vue'
import RolesSection from '@/components/RolesSection.vue'
import AppButton from '@/components/AppButton.vue'
import SecuritySection from '@/components/SecuritySection.vue'

const auth = useAuthStore()

// --- access flags -----------------------------------------------
// Derived from the loaded session user. isOwnProfile is always true
// here since this page only ever edits the logged-in user.
const isWorker = ref(false)
const canManageAccess = ref(false)
const isOwnProfile = ref(true)

// --- local state ---------------------------------------------------
const emptyUser = () => ({
  id: null,
  avatarUrl: '',
  personal: {
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    birthDate: '',
    country: '',
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
  },
  bio: '',
  permissions: [],
  roles: [],
})

const user = reactive(emptyUser())
const avatarFile = ref(null)

const isLoading = ref(true)
const loadError = ref('')
const isSaving = ref(false)
const saveError = ref('')
const saveSuccess = ref(false)
const errors = reactive({ personal: {}, business: {}, bio: '', security: {} })

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
  [user.personal.firstName, user.personal.lastName].filter(Boolean).join(' ')
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
      name: apiUser.first_name ?? apiUser.name ?? '',
      lastname: apiUser.last_name ?? apiUser.lastname ?? '',
      email: apiUser.email ?? '',
      phone: apiUser.phone ?? '',
      birthdate: apiUser.birth_date ?? apiUser.birthdate ?? '',
      country: apiUser.country ?? '',
    },
    security: {
      password: '',
      password_confirmation: '',
    },
    business: {
      companyName: apiUser.business?.company_name ?? '',
      taxId: apiUser.business?.tax_id ?? '',
      jobTitle: apiUser.business?.job_title ?? '',
      department: apiUser.business?.department ?? '',
      hourlyRate: apiUser.business?.hourly_rate ?? '',
      hireDate: apiUser.business?.hire_date ?? '',
      isContractor: apiUser.business?.is_contractor ?? false,
    },
    bio: apiUser.bio ?? '',
    permissions: apiUser.permissions ?? [],
    roles: apiUser.roles ?? [],
  }
}

// --- data loading ---------------------------------------------------
async function fetchUser() {
  isLoading.value = true
  loadError.value = ''
  try {
    const apiUser = auth.sessionUser // expected to return the raw user object (see README)
    Object.assign(user, mapApiUserToForm(apiUser))
    console.log(apiUser)

    isWorker.value = apiUser.type === 'worker' || apiUser.is_worker === true
    canManageAccess.value = (apiUser.permissions ?? []).includes('users.manage_access')
      || (apiUser.roles ?? []).includes('admin')
    
  } catch (err) {
    console.error(err)
    loadError.value = 'Could not load profile. Please try again.'
  } finally {
    isLoading.value = false
  }
}

// --- save -----------------------------------------------------------
async function handleSave() {
  isSaving.value = true
  saveError.value = ''
  saveSuccess.value = false
  try {
    // Replace with your real call, e.g.:
    // await auth.updateUser({
    //   first_name: user.personal.firstName,
    //   last_name: user.personal.lastName,
    //   email: user.personal.email,
    //   phone: user.personal.phone,
    //   birth_date: user.personal.birthDate,
    //   country: user.personal.country,
    //   bio: user.bio,
    //   ...(user.security.password ? {
    //     password: user.security.password,
    //     password_confirmation: user.security.password_confirmation,
    //   } : {}),
    // })
    // if (avatarFile.value) {
    //   const formData = new FormData()
    //   formData.append('avatar', avatarFile.value)
    //   await auth.updateAvatar(formData)
    // }
    saveSuccess.value = true
  } catch (err) {
    saveError.value = 'Could not save changes. Please try again.'
  } finally {
    isSaving.value = false
  }
}

function handleCancel() {
  fetchUser()
  avatarFile.value = null
  saveError.value = ''
  saveSuccess.value = false
}

function handleAvatarFile(file) {
  avatarFile.value = file
}

onMounted(fetchUser)
</script>
