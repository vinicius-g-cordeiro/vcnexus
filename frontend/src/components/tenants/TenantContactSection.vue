<template>
  <Fieldset legend="Contact" description="How customers and the system can reach this tenant.">
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :model-value="form.email" type="text" label="Email" placeholder="contact@VCNexus.com" :error="errors.email" required @update:model-value="updateField('email', $event)" />
      <BaseInput :model-value="form.phone" type="text" label="Phone" placeholder="+1 555 000 0000" :error="errors.phone" @update:model-value="updateField('phone', $event)" />
    </div>

    <BaseInput :model-value="form.website" type="text" label="Website" placeholder="https://VCNexus.com" :error="errors.website" @update:model-value="updateField('website', $event)" />

    <BaseInput :model-value="form.address" label="Address" placeholder="123 Main St, Suite 4, Austin, TX" :error="errors.address" @update:model-value="updateField('address', $event)" />
  </Fieldset>
</template>

<script setup>
/**
 * TenantContactSection.vue — email, phone, website, and address.
 *
 * Usage:
 * <TenantContactSection v-model="form.contact" :errors="errors.contact" />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { email, phone, website, address }
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

const form = computed(() => props.modelValue)
</script>
