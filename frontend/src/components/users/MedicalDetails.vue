<template>
  <Fieldset legend="Medical Details" description="Your name and contact information.">
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-3">
      <Select :model-value="form.blood_type" label="Blood Type" placeholder="Select a blood type..." :options="blood_typeOptions" @update:model-value="updateField('blood_type', $event)" />
      <Select :model-value="form.blood_factor" label="Blood Factor" placeholder="Select a blood factor..." :options="blood_factorOptions" @update:model-value="updateField('blood_factor', $event)" />
    </div>

  </Fieldset>
</template>

<script setup>
/**
 * PersonalDetailsSection.vue — first/last name, email, phone,
 * birth date, country. Uses BaseInput / Select from the shared kit.
 *
 * Usage:
 * <PersonalDetailsSection v-model="form.personal" :errors="errors.personal" :country-options="countries" />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'
import Select from '@/components/Select.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { name, lastname, email, phone, birthdate, country }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  blood_typeOptions: {
    type: Array,
    default: () => [
      { label: 'O', value: '1' },
      { label: 'AB', value: '2' },
      { label: 'B', value: '3' },
      { label: 'A', value: '4' },
    ],
  },
  blood_factorOptions: {
    type: Array,
    default: () => [
      { label: 'O', value: '1' },
      { label: 'AB', value: '2' },
      { label: 'B', value: '3' },
      { label: 'A', value: '4' },
    ],
  },
})

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

const form = computed(() => ({
  blood_type: props.modelValue.blood_type,
  blood_factor: props.modelValue.blood_factor,
}))
</script>
