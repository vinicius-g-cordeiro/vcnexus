<template>
  <fieldset :class="[
    'rounded-md m-2 border transition-colors',
    'dark:bg-neutral-800 bg-neutral-100',
    'dark:border-neutral-100/60 border-neutral-500/70',
    'shadow-sm dark:shadow-black/20',
    paddingClass,
  ]">
    <legend v-if="legend" class="-ml-2 px-2 font-semibold text-neutral-900 dark:text-neutral-50 text-sm uppercase">
      {{ legend }}
    </legend>
    <p v-if="description" class="mt-1 mb-4 text-neutral-500 dark:text-neutral-400 text-sm">
      {{ description }}
    </p>

    <!-- height adapts automatically to however many inputs are slotted in -->
    <div :class="['flex flex-col', gapClass]">
      <slot />
    </div>
  </fieldset>
</template>

<script setup>
/**
 * Fieldset.vue — consistent wrapper for grouping form inputs.
 * Subtle border + shadow, height auto-adapts to slotted content
 * (no fixed height is ever set), works with any number of inputs.
 *
 * Usage:
 * <Fieldset legend="Account details" description="Used for login and notifications.">
 *   <BaseInput v-model="form.email" label="Email" />
 *   <BaseInput v-model="form.password" type="password" label="Password" />
 * </Fieldset>
 *
 * For multi-column layouts, wrap fields in your own
 * <div class="gap-4 grid grid-cols-2"> inside the default slot —
 * Fieldset itself stays a single flex column.
 */
import { computed } from 'vue'

const props = defineProps({
  legend: {
    type: String,
    default: '',
  },
  description: {
    type: String,
    default: '',
  },
  gap: {
    type: String,
    default: 'md', // 'sm' | 'md' | 'lg'
  },
  padding: {
    type: String,
    default: 'md', // 'sm' | 'md' | 'lg'
  },
})

const gapClass = computed(() => {
  const map = { sm: 'gap-3', md: 'gap-5', lg: 'gap-7' }
  return map[props.gap] ?? map.md
})

const paddingClass = computed(() => {
  const map = { sm: 'p-4', md: 'p-6', lg: 'p-8' }
  return map[props.padding] ?? map.md
})
</script>
