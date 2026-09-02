<template>
  <label class="inline-flex items-center gap-2.5 cursor-pointer select-none" :class="disabled ? 'opacity-50 cursor-not-allowed' : ''">
    <span class="relative flex justify-center items-center w-5 h-5 shrink-0">
      <input
        v-model="value"
        type="checkbox"
        :disabled="disabled"
        class="sr-only peer"
        v-bind="$attrs"
      />
      <span
        :class="[
          'h-5 w-5 rounded border-2 transition-colors dark:bg-zinc-800 bg-neutral-100',
          'peer-checked:bg-emerald-500 peer-checked:border-emerald-500',
          'peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500 peer-focus-visible:ring-offset-1',
          error ? 'border-red-500' : 'dark:border-zinc-600 border-zinc-400',
        ]"
      />
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="absolute w-3.5 h-3.5 text-white transition-opacity pointer-events-none"
        :class="value ? 'opacity-100' : 'opacity-0'"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
      >
        <polyline points="20 6 9 17 4 12" />
      </svg>
    </span>
    <span v-if="label || $slots.default" class="text-zinc-900 dark:text-zinc-50 text-sm">
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
