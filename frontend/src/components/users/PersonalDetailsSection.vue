<template>
  <Fieldset legend="Personal details" description="Your name and contact information.">
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-3">
      <BaseInput :model-value="form.name" label="First name" placeholder="Joe" :error="errors.name" required @update:model-value="updateField('name', $event)" />
      <BaseInput :model-value="form.surname" label="Surname" placeholder="Jane" :error="errors.surname"  @update:model-value="updateField('surname', $event)" />
      <BaseInput :model-value="form.lastname" label="Last name" placeholder="Doe" :error="errors.lastname" required @update:model-value="updateField('lastname', $event)" />
    </div>

    <BaseInput :model-value="form.email" type="text" label="Email" placeholder="you123@gmail.com" :error="errors.email" required @update:model-value="updateField('email', $event)" />

    <BaseInput :model-value="form.username" label="Username" placeholder="Jane" :error="errors.username"  @update:model-value="updateField('username', $event)" />

    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      
      <Select :model-value="form.religion" label="Religion" placeholder="Select a religion..." :options="religionOptions" @update:model-value="updateField('religion', $event)" />  

      <Select :model-value="form.gender" label="Gender" placeholder="Select a gender..." :options="genderOptions" @update:model-value="updateField('gender', $event)" />  

      <Select :model-value="form.sexual_orientation" label="Sexual Orientation" placeholder="Select a sexual orientation..." :options="sexual_orientationOptions" @update:model-value="updateField('sexual_orientation', $event)" />  

      <Select :model-value="form.marital_status" label="Marital Status" placeholder="Select a marital status..." :options="marital_statusOptions" @update:model-value="updateField('marital_status', $event)" />  

    </div>
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :model-value="form.phone" type="text" label="Phone" placeholder="+55 61 9 0000-0000" :error="errors.phone" @update:model-value="updateField('phone', $event)" />
      <BaseInput :model-value="form.birthdate" type="date" label="Date of birth" :error="errors.birthdate" @update:model-value="updateField('birthdate', $event)" />
    </div>

    <Select :model-value="form.locale" label="Locale" placeholder="Select a locale..." :options="localeOptions" @update:model-value="updateField('locale', $event)" />
  </Fieldset>
</template>

<script setup>
/**
 * PersonalDetailsSection.vue — first/last name, email, phone,
 * birth date, locale. Uses BaseInput / Select from the shared kit.
 *
 * Usage:
 * <PersonalDetailsSection v-model="form.personal" :errors="errors.personal" :locale-options="countries" />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'
import Select from '@/components/Select.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { name, lastname, email, phone, birthdate, locale }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  localeOptions: {
    type: Array,
    default: () => [      
      { label: 'Brazilian Portuguese (PT-BR)', value: 'pt-BR' },
      { label: 'English (EN-US)', value: 'en-us' },
    ],
  },
  genderOptions: {
    type: Array,
    default: () => [
      { label: 'Male', value: '1' },
      { label: 'Female', value: '2' },
      { label: 'Other', value: '3' },
    ],
  },
  sexual_orientationOptions: {
    type: Array,
    default: () => [
      { label: 'Straight', value: '1' },
      { label: 'Bi', value: '2' },
      { label: 'Gay', value: '3' },
      { label: 'Lesbian', value: '4' },
      { label: 'Trans', value: '5' },
      { label: 'Other', value: '6' },
    ],
  },
  marital_statusOptions: {
    type: Array,
    default: () => [
      { label: 'Single', value: '1' },
      { label: 'Married', value: '2' },
      { label: 'Divorced', value: '3' },
      { label: 'Stable Union', value: '4' },
      { label: 'Widowed', value: '5' },
    ],
  },
  religionOptions: {
    type: Array,
    default: () => [
      { label: 'Catholic', value: '1' },
      { label: 'Protestant', value: '2' },
      { label: 'Muslim', value: '3' },
      { label: 'Other', value: '4' },
    ],
  },

})

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

const form = computed(() => ({
  name: props.modelValue.name,
  surname: props.modelValue.surname,
  lastname: props.modelValue.lastname,
  username: props.modelValue.username,
  email: props.modelValue.email,
  phone: props.modelValue.phone,
  birthdate: props.modelValue.birthdate,
  locale: props.modelValue.locale,
  gender: props.modelValue.gender,
  sexual_orientation: props.modelValue.sexual_orientation,
  religion: props.modelValue.religion,
  marital_status: props.modelValue.marital_status,
}))
</script>
