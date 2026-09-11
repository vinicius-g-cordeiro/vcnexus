<template>
  <Fieldset legend="Business details" description="Shown on invoices and to clients you work with.">

    <div class="flex-wrap gap-5 grid grid-cols-1 sm:grid-cols-2">
      <Select :disabled="disabled" :model-value="form.contractType" label="Contract Type" placeholder="Select the type of contract..." :options="contractTypeOptions" @update:model-value="updateField('contractType', $event)" />
      <template v-if="authStore.isSuperAdmin">
        <Select :disabled="disabled" :model-value="form.tenant" label="Tenant" placeholder="Select a tenant..." :options="tenantOptions" @focus="loadTenants" @update:model-value="updateField('tenant', $event)" />
      </template>
      <template v-else>
        <Select :disabled="disabled" v-show="false" :model-value="form.tenant" label="Tenant" placeholder="Select a tenant..." :options="[{ 'value': authStore.sessionUser.tenant_id }]" @update:model-value="updateField('tenant', $event)" />
      </template>

      <BaseInput :disabled="disabled" :model-value="form.hourlyRate" type="number" label="Hourly rate" placeholder="0.00" :error="errors.hourlyRate" @update:model-value="updateField('hourlyRate', $event)" />
      <BaseInput :disabled="disabled" :model-value="form.hireDate" type="date" label="Hire date" :error="errors.hireDate" @update:model-value="updateField('hireDate', $event)" />
    </div>
    <FileUpload name="contract" label="Contract" :disabled="disabled" :model-value="form.contractFile" :accept="'.pdf'" />

  </Fieldset>
</template>

<script setup>
/**
 * BusinessDetailsSection.vue — work-related fields for worker/staff
 * accounts: company, tax ID, job title, department, rate, hire date.
 *
 * Usage:
 * <BusinessDetailsSection v-model="form.business" :errors="errors.business" />
 */
import { ref, computed, onMounted } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'
import Select from '@/components/Select.vue'
import { storeToRefs } from 'pinia'
import { useTenantStore } from '@/stores/tenantStore'
import { useAuthStore } from '@/stores/authStore'
import FileUpload from '@/components/FileUpload.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { Contract Type, Tenant, Hourly Rate, Hire Date, Contract file. }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  jobTitleOptions: {
    type: Array,
    default: () => [
      { label: 'Technician', value: 'technician' },
      { label: 'Supervisor', value: 'supervisor' },
      { label: 'Manager', value: 'manager' },
      { label: 'Coordinator', value: 'coordinator' },
    ],
  },
  departmentOptions: {
    type: Array,
    default: () => [
      { label: 'Operations', value: 'operations' },
      { label: 'Field Services', value: 'field-services' },
      { label: 'Support', value: 'support' },
      { label: 'Administration', value: 'administration' },
    ],
  },
  contractTypeOptions: {
    type: Array,
    default: () => [
      { label: 'Employee', value: '1' },
      { label: 'Independent Contractor', value: '2' },
    ]
  },
  disabled: {
    type: Boolean,
    default: false
  },
})

const tenantStore = useTenantStore()
const authStore = useAuthStore()

const emit = defineEmits(['update:modelValue'])

const { tenants } = storeToRefs(tenantStore)

const tenantOptions = computed(() =>
  (tenants.value ?? []).map(tenant => ({
    label: tenant.legal_name ?? tenant.trade_name ?? tenant.name,
    value: tenant.id,
  }))
)

async function loadTenants() {
  if (!authStore.sessionUser.roles.includes('1')) { return }
  if (tenants?.length > 0) {
    return
  }
  await tenantStore.search({})
}

function updateField(key, value) {
  emit('update:modelValue', {
    ...props.modelValue,
    [key]: value,
  })
}

const form = computed(() => props.modelValue)
onMounted(async () => {
  await loadTenants()
})
</script>
