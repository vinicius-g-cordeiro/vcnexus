<template>
  <Fieldset legend="Business details" description="Shown on invoices and to clients you work with.">

    <Select :model-value="form.tenant" label="Tenant" placeholder="Select a tenant..." :options="tenantOptions" @focus="loadTenants" @update:model-value="updateField('tenant', $event)" />

    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :model-value="form.companyName" label="Company name" placeholder="VCNexus Services LLC" :error="errors.companyName" @update:model-value="updateField('companyName', $event)" />
      <BaseInput :model-value="form.taxId" label="Tax ID / VAT number" placeholder="00.000.000/0001-00" :error="errors.taxId" @update:model-value="updateField('taxId', $event)" />
    </div>

    <Select :model-value="form.jobTitle" label="Job title" placeholder="Select a title..." :options="jobTitleOptions" @update:model-value="updateField('jobTitle', $event)" />

    <Select :model-value="form.department" label="Department" placeholder="Select a department..." :options="departmentOptions" @update:model-value="updateField('department', $event)" />

    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :model-value="form.hourlyRate" type="number" label="Hourly rate" placeholder="0.00" :error="errors.hourlyRate" @update:model-value="updateField('hourlyRate', $event)" />
      <BaseInput :model-value="form.hireDate" type="date" label="Hire date" :error="errors.hireDate" @update:model-value="updateField('hireDate', $event)" />
    </div>

    <BaseCheckbox :model-value="form.isContractor" label="This worker is an independent contractor (not a direct employee)" @update:model-value="updateField('isContractor', $event)" />
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
import { computed, onMounted } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'
import BaseCheckbox from '@/components/BaseCheckbox.vue'
import Select from '@/components/Select.vue'
import { storeToRefs } from 'pinia'
import { useTenantStore } from '@/stores/tenantStore'


const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { companyName, taxId, jobTitle, department, hourlyRate, hireDate, isContractor }
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
})

const tenantStore = useTenantStore()

const emit = defineEmits(['update:modelValue'])

const { tenants } = storeToRefs(tenantStore)

const tenantOptions = computed(() =>
  (tenants.value ?? []).map(tenant => ({
    label: tenant.legal_name ?? tenant.trade_name ?? tenant.name,
    value: tenant.id,
  }))
)

async function loadTenants() {
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
