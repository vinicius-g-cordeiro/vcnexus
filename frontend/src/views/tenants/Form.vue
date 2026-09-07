<template>
  <div class="bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
    <div class="mx-auto px-4 sm:px-6 py-10 max-w-5xl">
      <!-- Page header -->
      <div class="flex flex-col mb-8">
        <div class="flex flex-row justify-between">
          <h1 class="font-semibold text-2xl tracking-tight">{{ isView ? 'View Tenant' : (isNew ? 'New tenant' : 'Edit Tenant') }}</h1>
          <template v-if="!isNew">
            <span class="ms-auto me-0" v-if="isView">
              <Button variant="outline" :to="{ name: 'tenants.edit', params: { uuid: tenant.uuid } }"><i class="bi bi-pencil-square"></i> Edit {{ tenant.legal.tradeName || tenant.legal.legalName }}</Button>
            </span>
            <span class="ms-auto me-0" v-else>
              <Button variant="outline" :to="{ name: 'tenants.view', params: { uuid: tenant.uuid } }"><i class="bi bi-eye"></i> View {{ tenant.legal.tradeName || tenant.legal.legalName }}</Button>
            </span>
          </template>
        </div>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400 text-sm">
          {{ isView ? 'View this tenant' : (isNew ? "Set up a new tenant's legal, contact and branding information." : 'Update this tenant') }}
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
        <button type="button" class="ml-2 font-medium underline underline-offset-2" @click="loadTenant">
          Try again
        </button>
      </div>

      <div v-else class="flex lg:flex-row flex-col gap-8">
        <!-- Section nav -->
        <div class="lg:w-56 shrink-0">
          <SettingsNav v-model="activeSection" :sections="sections" />
        </div>

        <!-- Active section -->
        <div class="flex flex-col flex-1 gap-6 min-w-0">
          <TenantLegalSection :disabled="isView === true" v-show="activeSection === 'legal'" v-model="tenant.legal" :errors="errors.legal" />

          <TenantContactSection :disabled="isView === true" v-show="activeSection === 'contact'" v-model="tenant.contact" :errors="errors.contact" />

          <TenantIdentitySection :disabled="isView === true" v-show="activeSection === 'identity'" v-model="tenant.identity" :errors="errors.identity" :base-url="baseUrl" />

          <TenantSettingsSection :disabled="isView === true" v-show="activeSection === 'settings'" v-model="tenant.settings" :tenant-categories="tenant.settings.categories" :errors="errors.settings" />

          <TenantCustomizationSection :disabled="isView === true" v-show="activeSection === 'customization'" v-model="tenant.customization" :tenant-name="tenant.legal.tradeName || tenant.legal.legalName" :tenant-bio="tenant.identity.bio" />

          <template v-if="isView === false">
            <p v-if="saveError" class="text-red-500 text-sm">{{ saveError }}</p>
            <p v-if="saveSuccess" class="text-emerald-500 text-sm">
              {{ isNew ? 'Tenant created.' : 'Changes saved.' }}
            </p>

            <!-- Save bar -->
            <div class="flex justify-end items-center gap-3 pt-2">
              <Button variant="ghost" :disabled="isSaving" @click="handleCancel">
                Cancel
              </Button>
              <Button :loading="isSaving" @click="handleSave">
                {{ isNew ? 'Create tenant' : 'Save' }}
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
 * TenantForm.vue — view/edit/create page for a tenant, mode
 * derived from the route (same pattern as UserForm.vue).
 *
 * MODE
 *   - route.name === 'tenants.view' -> "view" mode: loads the
 *     tenant, all fields disabled, "Edit" link in the header.
 *   - route param :uuid present, route.name !== 'tenants.view'
 *     -> "edit" mode: loads the tenant, fields editable, PUTs
 *     updates, "View" link in the header.
 *   - no :uuid param -> "create" mode: blank form, POSTs a new
 *     tenant.
 *
 * PROPS / ROUTING
 *   Expected route config, e.g.:
 *     { path: 'tenants/new',        name: 'tenants.new',  component: TenantForm }
 *     { path: 'tenants/:uuid',      name: 'tenants.view', component: TenantForm }
 *     { path: 'tenants/:uuid/edit', name: 'tenants.edit', component: TenantForm }
 *
 * WIRING UP YOUR API
 *   `mapApiTenantToForm()` / `mapFormToApiPayload()` are the two
 *   translation points — adjust the right-hand-side keys to match
 *   your backend's naming. `handleSave()` branches between
 *   `tenantStore.createTenant()` (create) and
 *   `tenantStore.updateTenant()` (edit) — replace with your real
 *   calls.
 */
import { reactive, ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTenantStore } from '@/stores/tenantStore'

import SettingsNav from '@/components/SettingsNav.vue'
import TenantLegalSection from '@/components/tenants/TenantLegalSection.vue'
import TenantContactSection from '@/components/tenants/TenantContactSection.vue'
import TenantIdentitySection from '@/components/tenants/TenantIdentitySection.vue'
import TenantCustomizationSection from '@/components/tenants/TenantCustomizationSection.vue'
import TenantSettingsSection from '@/components/tenants/TenantSettingsSection.vue'
import Button from '@/components/Button.vue'

const route = useRoute()
const router = useRouter()
const tenantStore = useTenantStore()


// --- mode -----------------------------------------------------------
// route param wins: presence of :uuid decides create vs edit/view.
const targetId = computed(() => route?.params?.uuid ?? null)
const isNew = computed(() => !targetId?.value)
const isView = computed(() => route.name === 'tenants.view' ?? null)

const baseUrl = 'app.vcnexus.com' // shown as the slug prefix in the Identity section

const sections = [
  { key: 'legal', label: 'Legal identity' },
  { key: 'settings', label: 'System Settings' },
  { key: 'contact', label: 'Contact' },
  { key: 'identity', label: 'Identity' },
  { key: 'customization', label: 'Customization' },
]

const activeSection = ref('legal')

// --- local state ---------------------------------------------------
const emptyTenant = () => ({
  uuid: null,
  legal: {
    legalName: '',
    tradeName: '',
    type: '',
    taxId: '',
    municipalRegistration: '',
    stateRegistration: '',
  },
  contact: {
    email: '',
    phone: '',
    website: '',
    address: '',
  },
  identity: {
    domain: '',
    slug: '',
    description: '',
  },
  settings: {
    modules: [],
    subscriptionPlan: 1,
    categories: [],
  },
  customization: {
    primaryColor: '#10B981',
    accentColor: '#D4AF37',
    backgroundColor: '#FFFFFF',
    textColor: '#171717',
    fontFamily: 'inter',
    buttonStyle: 'rounded',
  },
})

// const tenant = reactive(emptyTenant())
const errors = reactive({ legal: {}, contact: {}, identity: {}, settings: {} })

const isLoading = ref(true)
const loadError = ref('')
const isSaving = ref(false)
const saveError = ref('')
const saveSuccess = ref(false)

// --- mapping: raw API payload -> the shape the form sections expect --
// Adjust the right-hand-side keys to whatever your backend actually
// calls them; this is the ONE place that needs to change if your
// API's field names differ from what's listed here.
function mapApiTenantToForm(apiTenant) {
  return {
    uuid: apiTenant.uuid ?? apiTenant.id ?? null,
    legal: {
      legalName: apiTenant.legal_name ?? '',
      tradeName: apiTenant.trade_name ?? '',
      type: apiTenant.type ?? '',
      taxId: apiTenant.tax_id ?? '',
      municipalRegistration: apiTenant.municipal_registration ?? '',
      stateRegistration: apiTenant.state_registration ?? '',
    },
    contact: {
      email: apiTenant.email ?? '',
      phone: apiTenant.phone ?? '',
      website: apiTenant.website ?? '',
      address: apiTenant.address ?? '',
    },
    identity: {
      domain: apiTenant.domain ?? '',
      slug: apiTenant.slug ?? '',
      description: apiTenant.description ?? '',
    },
    settings: {
      modules: apiTenant.modules ?? [],
      subscriptionPlan: apiTenant.subscription_plan ?? null,
      categories: apiTenant.categories ?? [],
    },
    customization: {
      primaryColor: apiTenant?.primary_color ?? '#10B981',
      accentColor: apiTenant?.accent_color ?? '#D4AF37',
      backgroundColor: apiTenant?.background_color ?? '#FFFFFF',
      textColor: apiTenant?.text_color ?? '#171717',
      fontFamily: apiTenant?.font_family ?? 'inter',
      buttonStyle: apiTenant?.button_style ?? 'rounded',
    },
  }
}

// --- mapping: form state -> API payload ------------------------------
function mapFormToApiPayload() {
  return {
    legal_name: tenant.legal.legalName,
    trade_name: tenant.legal.tradeName,
    type: tenant.legal.type,
    tax_id: tenant.legal.taxId,
    municipal_registration: tenant.legal.municipalRegistration,
    state_registration: tenant.legal.stateRegistration,
    email: tenant.contact.email,
    phone: tenant.contact.phone,
    website: tenant.contact.website,
    address: tenant.contact.address,
    domain: tenant.identity.domain,
    slug: tenant.identity.slug,
    description: tenant.identity.description,
    modules: tenant.settings.modules,
    subscriptionPlan: tenant.settings.subscriptionPlan,
    categories: tenant.settings.categories,
    customization: {
      primary_color: tenant.customization.primaryColor,
      accent_color: tenant.customization.accentColor,
      background_color: tenant.customization.backgroundColor,
      text_color: tenant.customization.textColor,
      font_family: tenant.customization.fontFamily,
      button_style: tenant.customization.buttonStyle,
    },
  }
}
const tenant = emptyTenant()
// --- data loading ---------------------------------------------------
async function loadTenant() {
  isLoading.value = true
  loadError.value = ''
  try {
    if (isNew.value) {
      // Nothing to fetch: start from a blank record.
      Object.assign(tenant, emptyTenant())
    } else {
      await tenantStore.fetchTenant(targetId.value)
      Object.assign(tenant, mapApiTenantToForm(tenantStore.tenant[0]))
      console.log(tenant)
    }
  } catch (err) {
    loadError.value = isNew.value
      ? 'Could not prepare the form. Please try again.'
      : 'Could not load this tenant. Please try again.'
  } finally {
    isLoading.value = false
  }
}

// --- basic client-side validation -----------------------------------
function validate() {
  errors.legal = {}
  errors.contact = {}
  errors.identity = {}
  errors.settings = {}

  if (!tenant.legal.legalName) errors.legal.legalName = 'Legal name is required'
  if (!tenant.legal.type) errors.legal.type = 'Select a business type'
  if (!tenant.legal.taxId) errors.legal.taxId = 'Tax ID is required'

  if (!tenant.contact.email) errors.contact.email = 'Email is required'

  if (!tenant.identity.slug) errors.identity.slug = 'Slug is required'

  if (!tenant.settings.modules?.length) errors.settings.modules = 'Modules is required'
  if (!tenant.settings.subscriptionPlan) errors.settings.subscriptionPlan = 'Subscription plan is required'

  return (
    !Object.keys(errors.legal).length &&
    !Object.keys(errors.contact).length &&
    !Object.keys(errors.identity).length &&
    !Object.keys(errors.settings).length
  )
}

// --- save -----------------------------------------------------------
async function handleSave() {
  if (isView.value === true) {
    return
  }
  if (!validate()) return

  isSaving.value = true
  saveError.value = ''
  saveSuccess.value = false
  try {
    const payload = mapFormToApiPayload()

    const saved = isNew.value
      ? await tenantStore.createTenant(payload)
      : await tenantStore.updateTenant(targetId.value, payload)

    saveSuccess.value = true

    if (isNew.value && saved) {
      router.push({ name: 'tenants.list' })
    }
  } catch (err) {
    const fieldErrors = err?.response?.data?.errors
    if (fieldErrors) {
      Object.assign(errors, fieldErrors)
    }
    saveError.value = err?.response?.data?.message
      ?? (isNew.value ? 'Could not create tenant. Please try again.' : 'Could not save changes. Please try again.')
  } finally {
    isSaving.value = false
  }
}

function handleCancel() {
  if (isView.value === true) {
    return
  }
  if (isNew.value) {
    router.push({ name: 'tenants.list' })
    return
  }
  loadTenant()
  saveError.value = ''
  saveSuccess.value = false
}

onMounted(loadTenant)
</script>