<template>
  <fieldset :class="[
    'm-2 border transition-colors',
    'dark:bg-zinc-800 bg-zinc-100',
    'dark:border-neutral-100/60 border-neutral-500/70',
    'shadow-sm dark:shadow-black/20',
    paddingClass,
    borderClass
  ]">
    <legend v-if="legend" class="-ml-2 px-2 font-semibold text-zinc-900 dark:text-zinc-50 text-sm uppercase">
      <i class="bi" :class="icon"></i>
      {{ legend }}
    </legend>
    <p v-if="description" class="mt-1 mb-4 text-zinc-500 dark:text-zinc-400 text-sm">
      {{ description }}
    </p>

    <ul v-if="actions" class="flex flex-wrap justify-end gap-2 ms-auto mt-1 mb-4 text-zinc-500 dark:text-zinc-400 text-sm">
      <li v-for="action in actions" :key="action.url" class="" >
          <Button :to="action.url" variant="outline" >
            <i :class=action.icon></i>
            {{ action.name }} </Button>
      </li> 
    </ul>

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
import Button from './Button.vue'

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
  border: {
    type: String,
    default: 'xs', //'xs' | 'sm' | 'md' | 'lg'
  },
  icon: {
    type: String,
    default: ''
  },
  actions: {
    type: Array,
    default: []
  }
})

const gapClass = computed(() => {
  const map = { sm: 'gap-3', md: 'gap-5', lg: 'gap-7' }
  return map[props.gap] ?? map.md
})

const paddingClass = computed(() => {
  const map = { sm: 'p-4', md: 'p-6', lg: 'p-8' }
  return map[props.padding] ?? map.md
})

const borderClass = computed(() => {
  const map = { xs: 'rounded-xs', sm: 'rounded-sm', md: 'rounded-md', lg: 'rounded-lg' }
  return map[props.padding] ?? map.md
})
</script>
