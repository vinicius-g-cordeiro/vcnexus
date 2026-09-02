<template>
  <Fieldset legend="Business details" description="Shown on invoices and to clients you work with.">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <BaseInput
        :model-value="form.companyName"
        label="Company name" placeholder="Acme Services LLC" :error="errors.companyName"
        @update:model-value="updateField('companyName', $event)"
      />
      <BaseInput
        :model-value="form.taxId"
        label="Tax ID / VAT number" placeholder="00.000.000/0001-00" :error="errors.taxId"
        @update:model-value="updateField('taxId', $event)"
      />
    </div>

    <Select
      :model-value="form.jobTitle"
      label="Job title"
      placeholder="Select a title..."
      :options="jobTitleOptions"
      @update:model-value="updateField('jobTitle', $event)"
    />

    <Select
      :model-value="form.department"
      label="Department"
      placeholder="Select a department..."
      :options="departmentOptions"
      @update:model-value="updateField('department', $event)"
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <BaseInput
        :model-value="form.hourlyRate"
        type="number" label="Hourly rate" placeholder="0.00" :error="errors.hourlyRate"
        @update:model-value="updateField('hourlyRate', $event)"
      />
      <BaseInput
        :model-value="form.hireDate"
        type="date" label="Hire date" :error="errors.hireDate"
        @update:model-value="updateField('hireDate', $event)"
      />
    </div>

    <BaseCheckbox
      :model-value="form.isContractor"
      label="This worker is an independent contractor (not a direct employee)"
      @update:model-value="updateField('isContractor', $event)"
    />
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
import { computed } from 'vue'
import Fieldset from './Fieldset.vue'
import BaseInput from './BaseInput.vue'
import BaseCheckbox from './BaseCheckbox.vue'
import Select from './Select.vue'

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

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

const form = computed(() => props.modelValue)
</script>
