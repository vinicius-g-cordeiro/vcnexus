<template>
  <Fieldset legend="Personal details" description="Your name and contact information.">
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput
        :model-value="form.name"
        label="First name" placeholder="Jane" :error="errors.name" required
        @update:model-value="updateField('name', $event)"
      />
      <BaseInput
        :model-value="form.lastname"
        label="Last name" placeholder="Doe" :error="errors.lastname" required
        @update:model-value="updateField('lastname', $event)"
      />
    </div>

    <BaseInput
      :model-value="form.email"
      type="text" label="Email" placeholder="you@example.com" :error="errors.email" required
      @update:model-value="updateField('email', $event)"
    />

    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput
        :model-value="form.phone"
        type="text" label="Phone" placeholder="+1 555 000 0000" :error="errors.phone"
        @update:model-value="updateField('phone', $event)"
      />
      <BaseInput
        :model-value="form.birthDate"
        type="date" label="Date of birth" :error="errors.birthDate"
        @update:model-value="updateField('birthDate', $event)"
      />
    </div>

    <Select
      :model-value="form.country"
      label="Country"
      placeholder="Select a country..."
      :options="countryOptions"
      @update:model-value="updateField('country', $event)"
    />
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
    required: true, // { name, lastname, email, phone, birthDate, country }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  countryOptions: {
    type: Array,
    default: () => [
      { label: 'United States', value: 'us' },
      { label: 'Brazil', value: 'br' },
      { label: 'Portugal', value: 'pt' },
      { label: 'United Kingdom', value: 'gb' },
    ],
  },
})

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

const form = computed(() => ({
  name: props.modelValue.name,
  lastname: props.modelValue.lastname,
  email: props.modelValue.email,
  phone: props.modelValue.phone,
  birthDate: props.modelValue.birthDate,
  country: props.modelValue.country,
}))
</script>
