<template>
  <fieldset class="flex flex-col gap-2">
    <legend v-if="label" class="mb-1 font-medium text-zinc-900 dark:text-zinc-50 text-sm">
      {{ label }}
    </legend>

    <label v-for="option in options" :key="option.value" class="inline-flex items-center gap-2.5 cursor-pointer select-none" :class="disabled ? 'opacity-50 cursor-not-allowed' : ''">
      <span class="relative flex justify-center items-center w-5 h-5 shrink-0">
        <input v-model="value" type="radio" :name="groupName" :value="option.value" :disabled="disabled" class="sr-only peer" />
        <span class="bg-neutral-100 dark:bg-zinc-800 border-2 border-neutral-400 dark:border-neutral-600 peer-checked:border-emerald-500 rounded-full peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500 peer-focus-visible:ring-offset-1 w-5 h-5 transition-colors" />
        <span class="absolute bg-emerald-500 rounded-full w-2.5 h-2.5 transition-opacity pointer-events-none" :class="value === option.value ? 'opacity-100' : 'opacity-0'" />
      </span>
      <span class="text-zinc-900 dark:text-zinc-50 text-sm">{{ option.label }}</span>
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
