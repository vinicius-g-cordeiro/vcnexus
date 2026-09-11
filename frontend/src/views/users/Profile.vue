<template>
  <div class="bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
    <div class="mx-auto px-4 sm:px-6 py-10 max-w-5xl">
      <!-- Page header -->
      <div class="mb-8">
        <h1 class="font-semibold text-2xl tracking-tight">My Profile</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400 text-sm">
          Update your profile
          <i class="bi bi-person-gear"></i>
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
          <AvatarUpload v-show="activeSection === 'avatar'" v-model="user.avatarUrl" :name="fullName" @update:file="handleAvatarFile" />

          <PersonalDetailsSection v-show="activeSection === 'personal'" v-model="user.personal" :errors="errors.personal" />

          <SecuritySection v-show="activeSection === 'security'" v-model="user.security" :errors="errors.security" :password-required="false" />

          <BusinessDetailsSection v-show="activeSection === 'business' && auth.isWorker" v-model="user.business" :errors="errors.business" />

          <BioSection v-show="activeSection === 'bio'" v-model="user.bio" :error="errors.bio" />

          <p v-if="saveError" class="text-red-500 text-sm">{{ saveError }}</p>
          <p v-if="saveSuccess" class="text-emerald-500 text-sm">Changes saved.</p>

          <!-- Save bar -->
          <div class="flex justify-end items-center gap-3 pt-2">
            <Button variant="ghost" :disabled="isSaving" @click="handleCancel">
              Cancel
            </Button>
            <Button :loading="isSaving" @click="handleSave">
              Save
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * Profile.vue — "my own profile" page for the logged-in session
 * user. Routed to directly (e.g. `path: 'profile'`), no `:id` param.
 *
 * DATA SOURCE
 *   Reads and writes `authStore` exclusively, via its `sessionUser`
 *   state and `fetchUser` / `updateUser` / `updateAvatar` actions.
 *   This page always targets `auth.sessionUser`, never an arbitrary
 *   user by id — for an admin-style "edit any user" page, see
 *   UserForm.vue, backed by a separate `userStore` instead, so
 *   "who's logged in" and "who's being edited" never conflate.
 *
 * PERMISSIONS
 *   `auth.isWorker` and `auth.canManageAccess` are store getters
 *   derived from `sessionUser.role`, used directly in the template
 *   rather than copied into local refs — one source of truth.
 *
 * STORE ACTION SHAPES
 *   `auth.updateUser(payload)` and `auth.updateAvatar(formData)`
 *   return a boolean (success/failure) and update `auth.sessionUser`
 *   themselves, per authStore's existing convention (matching
 *   `login`/`logout`) — this component reads `auth.sessionUser`
 *   afterward rather than expecting a returned user object.
 *
 * PASSWORD CHANGES
 *   Password fields are optional on this page (leave blank to keep
 *   the current password). If your API requires re-authentication
 *   to change a password, `user.security.currentPassword` is wired
 *   up for that — drop it if your backend doesn't need it.
 */
import { reactive, ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'

import SettingsNav from '@/components/SettingsNav.vue'
import AvatarUpload from '@/components/users/AvatarUpload.vue'
import PersonalDetailsSection from '@/components/users/PersonalDetailsSection.vue'
import BusinessDetailsSection from '@/components/users/BusinessDetailsSection.vue'
import BioSection from '@/components/users/BioSection.vue'
import PermissionsSection from '@/components/users/PermissionsSection.vue'
import RolesSection from '@/components/users/RolesSection.vue'
import Button from '@/components/Button.vue'
import SecuritySection from '@/components/users/SecuritySection.vue'

import { useRouter } from 'vue-router'
const storageBase = import.meta.env.VITE_API_URL
const router = useRouter()

const auth = useAuthStore()

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
    currentPassword: '',
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
const errors = reactive({ personal: {}, business: {}, bio: '', security: {} })

// --- sections -----------------------------------------------------
const allSections = [
  { key: 'avatar', label: 'Avatar' },
  { key: 'personal', label: 'Personal details' },
  { key: 'business', label: 'Business details', requires: 'worker' },
  { key: 'bio', label: 'Bio' },
  { key: 'security', label: 'Security' },
]

const visibleSections = computed(() =>
  allSections.filter((s) => {
    if (s.requires === 'worker') return auth.isWorker
    if (s.requires === 'access') return auth.canManageAccess
    return true
  })
)

const activeSection = ref('avatar')

const fullName = computed(() =>
  [user.personal.name, user.personal.lastname].filter(Boolean).join(' ')
)

// --- mapping: raw API user -> the shape the form sections expect --
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
      email: apiUser.email ?? '',
      phone: apiUser.phone ?? '',
      birthdate: apiUser.birthdate ?? '',
      username: apiUser.username ?? '',
      locale: apiUser.locale ?? '',
      gender: apiUser.gender,
      religion: apiUser.religion,
      marital_status: apiUser.marital_status,
      sexual_orientation: apiUser.sexual_orientation,
    },
    security: {
      currentPassword: '',
      password: '',
      password_confirmation: '',
    },
    business: {
      companyName: apiUser.organization_name ?? '',
      taxId: apiUser.tax_id ?? '',
      jobTitle: apiUser.business?.job_title ?? '',
      department: apiUser.business?.department ?? '',
      hourlyRate: apiUser.business?.hourly_rate ?? '',
      hireDate: apiUser.business?.hire_date ?? '',
      isContractor: apiUser.business?.is_contractor ?? false,
      tenant: apiUser.tenant_id ?? null,
    },
    medical: {
      blood_factor: apiUser.medical?.blood_factor ?? '',
      blood_type: apiUser.medical?.blood_type ?? '',
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
    id: auth.sessionUser.id,
    uuid: auth.sessionUser.uuid,
    name: user.personal.name,
    surname: user.personal.surname,
    lastname: user.personal.lastname,
    email: user.personal.email,
    phone: user.personal.phone,
    birthdate: user.personal.birthdate,
    locale: user.personal.locale,
    username: user.personal.username,
    blood_factor: user.personal.blood_factor,
    blood_type: user.personal.blood_type,
    gender: user.personal.gender,
    religion: user.personal.religion,
    marital_status: user.personal.marital_status,
    sexual_orientation: user.personal.sexual_orientation,
    bio: user.bio,
    // Password change is optional here: only send password fields
    // if the user actually typed a new password.
    ...(user.security.password
      ? {
        current_password: user.security.currentPassword,
        password: user.security.password,
        password_confirmation: user.security.password_confirmation,
      }
      : {}),
    ...(auth.isWorker
      ? {
        business: {
          company_name: user.business.companyName,
          tax_id: user.business.taxId,
          job_title: user.business.jobTitle,
          department: user.business.department,
          hourly_rate: user.business.hourlyRate,
          hire_date: user.business.hireDate,
          is_contractor: user.business.isContractor,
          tenant: user.business.tenant,
        },
      }
      : {}),
    // Most APIs won't let a user grant themselves permissions/roles
    // via a self-service profile save, even if they can view them.
    // Included here for parity; drop this block if your backend
    // rejects (or silently ignores) it on self-edit.
    ...(auth.canManageAccess
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
    const ok = await auth.fetchUser()
    if (!ok || !auth.sessionUser) {
      loadError.value = 'Could not load your profile. Please try again.'
      return
    }
    Object.assign(user, mapApiUserToForm(auth.sessionUser))
  } catch (err) {
    console.error(err)
    loadError.value = 'Could not load your profile. Please try again.'
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
    const payload = mapFormToApiPayload()
    const ok = await auth.updateUser(payload)

    if (!ok) {
      saveError.value = auth.error || 'Could not save changes. Please try again.'
      return
    }

    if (avatarFile.value) {
      const formData = new FormData()
      formData.append('avatar', avatarFile.value)
      const avatarOk = await auth.updateAvatar(formData)
      if (!avatarOk) {
        saveError.value = auth.error || 'Profile saved, but the avatar upload failed.'
        return
      }
    }

    // authStore actions update sessionUser themselves; re-sync the
    // form from it rather than trusting a locally-held copy.
    if (auth.sessionUser) {
      Object.assign(user, mapApiUserToForm(auth.sessionUser))
    }

    saveSuccess.value = true
    user.security.currentPassword = ''
    user.security.password = ''
    user.security.password_confirmation = ''
    avatarFile.value = null


  } finally {
    isSaving.value = false
    router.push({ name: 'dashboard' })
  }
}

function handleCancel() {
  // No re-fetch needed: reset straight from the already-loaded
  // session user rather than hitting the API again.
  if (auth.sessionUser) {
    Object.assign(user, mapApiUserToForm(auth.sessionUser))
  }
  avatarFile.value = null
  saveError.value = ''
  saveSuccess.value = false
}

function handleAvatarFile(file) {
  avatarFile.value = file
}

onMounted(loadUser)
</script>