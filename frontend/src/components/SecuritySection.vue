<template>
  <Fieldset legend="Password" description="Your password and multi step verification ">
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput type="password"
        :model-value="form.password"
        label="Password" placeholder="Fill this if wants to change password" :error="errors.password" required
        @update:model-value="updateField('password', $event)" aria-autocomplete="new-password" autocomplete="new-password"
      />
      <BaseInput type="password"
        value = ''
        label="Password confirmation" placeholder="Confirm password" :error="errors.password_confirmation" required
        @update:model-value="updateField('password_confirmation', $event)" aria-autocomplete="new-password" autocomplete="new-password"
      />
    </div>
  </Fieldset>
</template>

<script setup>
/**
 * SecuritySection.vue - password , password_confirmation
 *
 * Usage:
 * <SecuritySection v-model="form.security" :errors="errors.security"  />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { password, password_confirmation }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

const form = computed(() => ({
  password: props.modelValue.password,
  password_confirmation: props.modelValue.password_confirmation,
  
}))
</script>
