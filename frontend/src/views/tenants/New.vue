<template>
  <div class="bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
    <div class="mx-auto px-4 sm:px-6 py-10 max-w-5xl">
      <!-- Page header -->
      <div class="mb-8">
        <h1 class="font-semibold text-2xl tracking-tight">New tenant</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400 text-sm">
          Set up a new tenant's legal, contact and branding information.
        </p>
      </div>

      <div class="flex lg:flex-row flex-col gap-8">
        <!-- Section nav -->
        <div class="lg:w-56 shrink-0">
          <SettingsNav v-model="activeSection" :sections="sections" />
        </div>

        <!-- Active section -->
        <div class="flex flex-col flex-1 gap-6 min-w-0">
          <TenantLegalSection v-show="activeSection === 'legal'" v-model="tenant.legal" :errors="errors.legal" />

          <TenantContactSection v-show="activeSection === 'contact'" v-model="tenant.contact" :errors="errors.contact" />

          <TenantIdentitySection v-show="activeSection === 'identity'" v-model="tenant.identity" :errors="errors.identity" :base-url="baseUrl" />

          <TenantSettingsSection v-show="activeSection === 'settings'" v-model="tenant.settings" :errors="errors.settings" />

          <TenantCustomizationSection v-show="activeSection === 'customization'" v-model="tenant.customization" :tenant-name="tenant.legal.tradeName || tenant.legal.legalName" :tenant-bio="tenant.identity.bio" />

          <p v-if="saveError" class="text-red-500 text-sm">{{ saveError }}</p>

          <!-- Save bar -->
          <div class="flex justify-end items-center gap-3 pt-2">
            <Button variant="ghost" :disabled="isSaving" @click="handleCancel">
              Cancel
            </Button>
            <Button :loading="isSaving" @click="handleCreate">
              Create tenant
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * NewTenant.vue — tenant creation page, routed to directly (e.g.
 * `path: 'tenants/new'`). Self-contained: holds its own form state
 * and performs its own create request — no props required from the
 * router.
 *
 * SECTIONS
 *   - Legal identity: legal name, trade name, type (MEI/LTDA/Inc),
 *     tax ID, municipal + state registration
 *   - Contact: email, phone, website, address
 *   - Identity: custom domain, slug (auto-slugified), bio, description
 *   - Website customization: colors, font, button style, with a
 *     live preview of the tenant's public site styling
 *
 * WIRING UP YOUR API
 *   Replace `handleCreate()`'s placeholder with your real call
 *   (fetch/axios/Pinia action). The full `tenant` object plus a
 *   `mapFormToApiPayload()` translation point is provided below —
 *   adjust the right-hand-side keys to match your backend's naming.
 */
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useTenantStore } from '@/stores/tenantStore'

import SettingsNav from '@/components/SettingsNav.vue'
import TenantLegalSection from '@/components/tenants/TenantLegalSection.vue'
import TenantContactSection from '@/components/tenants/TenantContactSection.vue'
import TenantIdentitySection from '@/components/tenants/TenantIdentitySection.vue'
import TenantCustomizationSection from '@/components/tenants/TenantCustomizationSection.vue'
import TenantSettingsSection from '@/components/tenants/TenantSettingsSection.vue'
import Button from '@/components/Button.vue'

const router = useRouter()
const tenantStore = useTenantStore()

const { tenants, error, loading } = storeToRefs(tenantStore)

const baseUrl = 'app.vcnexus.com' // shown as the slug prefix in the Identity section

const sections = [
  { key: 'legal', label: 'Legal identity' },
  { key: 'settings', label: 'System Settings'},
  { key: 'contact', label: 'Contact' },
  { key: 'identity', label: 'Identity' },
  { key: 'customization', label: 'Customization' },
]

const activeSection = ref('legal')

// --- local state ---------------------------------------------------
const emptyTenant = () => ({
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
    module: [],
    subscriptionPlan: 1,
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

const tenant = reactive(emptyTenant())
const errors = reactive({ legal: {}, contact: {}, identity: {} })

const isSaving = ref(false)
const saveError = ref('')

// --- mapping: form shape -> API payload -----------------------------
// Adjust the right-hand-side keys to whatever your backend expects;
// this is the one place to change if your API's field names differ.
function mapFormToApiPayload(t) {
  return {
    legal_name: t.legal.legalName,
    trade_name: t.legal.tradeName,
    type: t.legal.type,
    tax_id: t.legal.taxId,
    municipal_registration: t.legal.municipalRegistration,
    state_registration: t.legal.stateRegistration,
    email: t.contact.email,
    phone: t.contact.phone,
    website: t.contact.website,
    address: t.contact.address,
    domain: t.identity.domain,
    slug: t.identity.slug,
    description: t.identity.description,
    modules: t.identity.modules,
    subscriptionPlan: t.identity.subscriptionPlan,
    customization: {
      primary_color: t.customization.primaryColor,
      accent_color: t.customization.accentColor,
      background_color: t.customization.backgroundColor,
      text_color: t.customization.textColor,
      font_family: t.customization.fontFamily,
      button_style: t.customization.buttonStyle,
    },
  }
}

// --- basic client-side validation -----------------------------------
function validate() {
  errors.legal = {}
  errors.contact = {}
  errors.identity = {}

  if (!tenant.legal.legalName) errors.legal.legalName = 'Legal name is required'
  if (!tenant.legal.type) errors.legal.type = 'Select a business type'
  if (!tenant.legal.taxId) errors.legal.taxId = 'Tax ID is required'

  if (!tenant.contact.email) errors.contact.email = 'Email is required'

  if (!tenant.identity.slug) errors.identity.slug = 'Slug is required'

  return (
    !Object.keys(errors.legal).length &&
    !Object.keys(errors.contact).length &&
    !Object.keys(errors.identity).length
  )
}

// --- create -----------------------------------------------------------
async function handleCreate() {
  if (!validate()) return

  isSaving.value = true
  saveError.value = ''
  try {
    const payload = mapFormToApiPayload(tenant)

    console.log(tenantStore)
    // Replace with your real call, e.g.:
    const created = await tenantStore.register(payload)
    if(created){
      router.push({ name: 'tenants-list' })
    }
  } catch (err) {
    console.error(err)
    saveError.value = 'Could not create tenant. Please try again.'
  } finally {
    isSaving.value = false
  }
}

function handleCancel() {
  router.back()
}
</script>
