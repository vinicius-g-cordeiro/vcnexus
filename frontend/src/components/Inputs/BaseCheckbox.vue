<template>
  <label class="inline-flex items-center gap-2.5 cursor-pointer select-none" :class="disabled ? 'opacity-50 cursor-not-allowed' : ''">
    <span class="relative flex items-center justify-center h-5 w-5 shrink-0">
      <input
        v-model="value"
        type="checkbox"
        :disabled="disabled"
        class="peer sr-only"
        v-bind="$attrs"
      />
      <span
        :class="[
          'h-5 w-5 rounded border-2 transition-colors dark:bg-neutral-800 bg-neutral-200',
          'peer-checked:bg-emerald-500 peer-checked:border-emerald-500',
          'peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500 peer-focus-visible:ring-offset-1',
          error ? 'border-red-500' : 'dark:border-neutral-600 border-neutral-400',
        ]"
      />
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="absolute h-3.5 w-3.5 text-white pointer-events-none transition-opacity"
        :class="value ? 'opacity-100' : 'opacity-0'"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
      >
        <polyline points="20 6 9 17 4 12" />
      </svg>
    </span>
    <span v-if="label || $slots.default" class="text-sm dark:text-neutral-50 text-neutral-900">
      <slot>{{ label }}</slot>
    </span>
  </label>
</template>

<script setup>
/**
 * BaseCheckbox.vue — Tailwind-styled checkbox.
 * Usage:
 * <BaseCheckbox v-model="form.agree" label="I agree to the terms" />
 */
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  label: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

defineOptions({ inheritAttrs: false })

const value = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})
</script>
