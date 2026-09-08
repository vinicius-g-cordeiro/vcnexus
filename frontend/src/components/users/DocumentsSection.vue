<template>
  <Fieldset legend="Document details">

    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :disabled="disabled" :model-value="form.national_id" type="text" label="National ID" placeholder="" :error="errors.national_id" @update:model-value="updateField('national_id', $event)" />
      <BaseInput :disabled="disabled" :model-value="form.national_id_issuer" type="text" label="National ID Issuer" placeholder="Ex: SSP/DF" :error="errors.national_id_issuer" @update:model-value="updateField('national_id_issuer', $event)" />
    </div>
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :disabled="disabled" :model-value="form.taxpayer_id" type="text" label="Registration" placeholder="Ex: 000.000.000-00" :error="errors.taxpayer_id" @update:model-value="updateField('taxpayer_id', $event)" />
      <BaseInput :disabled="disabled" :model-value="form.drivers_license" type="text" label="Driver's License" :error="errors.drivers_license" @update:model-value="updateField('drivers_license', $event)" />
    </div>
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
  }
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
