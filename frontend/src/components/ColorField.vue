<template>
  <div class="flex flex-col gap-1.5">
    <label :for="inputId" class="text-sm font-medium dark:text-neutral-50 text-neutral-900">
      {{ label }}
    </label>
    <div class="flex items-center gap-2.5">
      <span class="relative h-9 w-9 shrink-0 rounded-md overflow-hidden border dark:border-neutral-600 border-neutral-400">
        <input
          :id="inputId"
          type="color"
          :value="modelValue"
          class="absolute inset-0 h-[150%] w-[150%] -top-[25%] -left-[25%] cursor-pointer border-none p-0"
          @input="$emit('update:modelValue', $event.target.value)"
        />
      </span>
      <input
        type="text"
        :value="modelValue"
        placeholder="#10B981"
        class="w-28 rounded-md px-2.5 py-1.5 text-sm dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900 border border-transparent focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
        @input="$emit('update:modelValue', $event.target.value)"
      />
    </div>
  </div>
</template>

<script setup>
/**
 * ColorField.vue — native color-picker swatch paired with a text
 * input showing/accepting the hex value directly.
 *
 * Usage:
 * <ColorField v-model="theme.primaryColor" label="Primary color" />
 */
defineProps({
  modelValue: { type: String, default: '#10B981' },
  label: { type: String, default: 'Color' },
})

defineEmits(['update:modelValue'])

const inputId = `color-${Math.random().toString(36).slice(2, 9)}`
</script>
