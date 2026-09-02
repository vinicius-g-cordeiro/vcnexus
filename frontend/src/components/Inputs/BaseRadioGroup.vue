<template>
  <fieldset class="flex flex-col gap-2">
    <legend v-if="label" class="text-sm font-medium dark:text-neutral-50 text-neutral-900 mb-1">
      {{ label }}
    </legend>

    <label
      v-for="option in options"
      :key="option.value"
      class="inline-flex items-center gap-2.5 cursor-pointer select-none"
      :class="disabled ? 'opacity-50 cursor-not-allowed' : ''"
    >
      <span class="relative flex items-center justify-center h-5 w-5 shrink-0">
        <input
          v-model="value"
          type="radio"
          :name="groupName"
          :value="option.value"
          :disabled="disabled"
          class="peer sr-only"
        />
        <span
          class="h-5 w-5 rounded-full border-2 transition-colors dark:border-neutral-600 border-neutral-400
                 dark:bg-neutral-800 bg-neutral-200
                 peer-checked:border-emerald-500
                 peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500 peer-focus-visible:ring-offset-1"
        />
        <span
          class="absolute h-2.5 w-2.5 rounded-full bg-emerald-500 transition-opacity pointer-events-none"
          :class="value === option.value ? 'opacity-100' : 'opacity-0'"
        />
      </span>
      <span class="text-sm dark:text-neutral-50 text-neutral-900">{{ option.label }}</span>
    </label>
  </fieldset>
</template>

<script setup>
/**
 * BaseRadioGroup.vue — group of Tailwind-styled radio buttons.
 * Usage:
 * <BaseRadioGroup
 *   v-model="form.plan"
 *   label="Choose a plan"
 *   :options="[{ label: 'Basic', value: 'basic' }, { label: 'Pro', value: 'pro' }]"
 * />
 */
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  options: {
    type: Array,
    required: true, // [{ label, value }]
  },
  label: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  name: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const groupName = props.name || `radio-${Math.random().toString(36).slice(2, 9)}`

const value = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})
</script>
