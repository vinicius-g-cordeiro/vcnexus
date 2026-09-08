<template>
  <Fieldset legend="Settings" description="Settings for what the tenant can see and not see on the system">

    <div class="gap-5 grid grid-cols-2 sm:grid-cols-2">
      <Select v-model="form.subscriptionPlan" label="Subscription Plan" :searchable="true" :clearable="false" :options="subscriptionPlanOptions" @update:model-value="updateField('subscriptionPlan', $event)" />

      <Select v-model="form.modules" multiple label="Modules" placeholder="Add modules..." :options="modulesOptions" @update:model-value="updateField('modules', $event)" />
    </div>

    <div class="gap-5 grid grid-cols-2 sm:grid-cols-2">
      <Select v-model="form.categories" multiple label="Category" placeholder="Add categories..." :options="categoryOptions" @update:model-value="updateField('categories', $event)"/>
    </div>
  </Fieldset>
</template>

<script setup>
/**
 * TenantSettingsSection.vue — 
 * Usage:
 * <TenantSettingsSection v-model="form.settings" :errors="errors.settings" />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import Select from '@/components/Select.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { domain, slug, bio, description }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  subscriptionPlanOptions: {
    type: Array,
    default: () => [
      { label: 'Free', value: '1' },
      { label: 'Copper', value: '2' },
      { label: 'Silver', value: '3' },
      { label: 'Gold', value: '4' },
      { label: 'Platinum', value: '5' },
      { label: 'Diamond', value: '6' },
    ],
  },
  modulesOptions: {
    type: Array,
    default: () => [
      { label: 'Users', value: '1' },
      { label: 'Schedules', value: '2' },
      { label: 'Tasks', value: '3' },
      { label: 'Tenants', value: '4' },
      { label: 'Store', value: '5' },
      { label: 'Payments', value: '6' },
      { label: 'Events', value: '7' },
      { label: 'Reports', value: '8' },
      { label: 'Associates', value: '9' },
      { label: 'Bills', value: '10' },
    ],
  },
  categoryOptions: {
    type: Array,
    default: () => [
      { label: 'Agriculture & Farming', value: '0' },
      { label: 'Automotive', value: '1' },
      { label: 'Beauty & Personal Care', value: '2' },
      { label: 'Construction', value: '3' },
      { label: 'Education', value: '4' },
      { label: 'Entertainment & Media', value: '5' },
      { label: 'Finance & Accounting', value: '6' },
      { label: 'Food & Beverage', value: '7' },
      { label: 'Healthcare', value: '8' },
      { label: 'Hospitality & Tourism', value: '9' },
      { label: 'Information Technology', value: '10' },
      { label: 'Manufacturing', value: '11' },
      { label: 'Marketing & Advertising', value: '12' },
      { label: 'Nonprofit & Associations', value: '13' },
      { label: 'Professional Services', value: '14' },
      { label: 'Real Estate', value: '15' },
      { label: 'Retail', value: '16' },
      { label: 'Telecommunications', value: '17' },
      { label: 'Transportation & Logistics', value: '18' },
      { label: 'Wholesale & Distribution', value: '19' },
      { label: 'Energy & Utilities', value: '20' },
      { label: 'Government & Public Services', value: '21' },
      { label: 'Other', value: '22' },
    ]
  },
  disabled: {
    type: Boolean,
    default: false
  }

})

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

const form = computed(() => props.modelValue)

</script>
