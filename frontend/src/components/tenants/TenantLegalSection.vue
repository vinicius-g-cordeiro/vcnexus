<template>
  <Fieldset legend="Legal identity" description="Official registration details for this tenant.">
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :model-value="form.legalName" label="Legal name" placeholder="VCNexus Serviços Ltda." :error="errors.legalName" required @update:model-value="updateField('legalName', $event)" />
      <BaseInput :model-value="form.tradeName" label="Trade name" placeholder="VCNexus" :error="errors.tradeName" @update:model-value="updateField('tradeName', $event)" />
    </div>

    <Select :model-value="form.type" label="Business type" placeholder="Select a type..." :searchable="true" :clearable="false" :options="typeOptions" :error="errors.type" @update:model-value="updateField('type', $event)" />

    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :model-value="form.taxId" label="Tax ID" placeholder="00.000.000/0001-00" :error="errors.taxId" required @update:model-value="updateField('taxId', $event)" />
      <BaseInput :model-value="form.municipalRegistration" label="Municipal registration" placeholder="000000000" :error="errors.municipalRegistration" @update:model-value="updateField('municipalRegistration', $event)" />
    </div>

    <BaseInput :model-value="form.stateRegistration" label="State registration" placeholder="000.000.000.000" :error="errors.stateRegistration" @update:model-value="updateField('stateRegistration', $event)" />
  </Fieldset>
</template>

<script setup>
/**
 * TenantLegalSection.vue — legal name, trade name, business type,
 * and the three registration numbers (tax ID, municipal, state).
 *
 * `type` options default to MEI / LTDA / Inc but are overridable in
 * case a given deployment needs more (e.g. LLC, S.A., etc).
 *
 * Usage:
 * <TenantLegalSection v-model="form.legal" :errors="errors.legal" />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'
import Select from '@/components/Select.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { legalName, tradeName, type, taxId, municipalRegistration, stateRegistration }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  typeOptions: {
    type: Array,
    default: () => [
      { label: 'MEI (Microempreendedor Individual)', value: '1' },
      { label: 'SLU (Sociedade Limitada Unipessoal)', value: '2' },
      { label: 'EI (Empresário Individual)', value: '3' },
      { label: 'LTDA (Sociedade Limitada)', value: '4' },
      { label: 'SS (Sociedade Simples)', value: '5' },
      { label: 'S.A. (Sociedade Anônima)', value: '5' },
    ],
  },
})

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

const form = computed(() => props.modelValue)
</script>
