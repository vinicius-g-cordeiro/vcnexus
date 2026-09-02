<template>
  <form
    role="search"
    :class="['relative w-full', maxWidthClass]"
    @submit.prevent="handleSubmit"
  >
    <label :for="inputId" class="sr-only">{{ placeholder }}</label>

    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 dark:text-neutral-400 text-neutral-500"
      viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <circle cx="11" cy="11" r="8" />
      <line x1="21" y1="21" x2="16.65" y2="16.65" />
    </svg>

    <input
      :id="inputId"
      v-model="query"
      type="search"
      :placeholder="placeholder"
      :class="[
        'w-full rounded-md py-2 pl-9 pr-9 text-sm transition-colors',
        'dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900',
        'placeholder:text-neutral-500 dark:placeholder:text-neutral-400',
        'border border-transparent focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500',
      ]"
    />

    <button
      v-if="query"
      type="button"
      class="absolute right-2.5 top-1/2 -translate-y-1/2 dark:text-neutral-400 text-neutral-500 hover:text-emerald-500"
      aria-label="Clear search"
      @click="clear"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
      </svg>
    </button>
  </form>
</template>

<script setup>
/**
 * SearchBar.vue — simple search input with icon and clear button.
 *
 * Usage:
 * <SearchBar v-model="searchQuery" placeholder="Search products..." @submit="onSearch" />
 */
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Search...',
  },
  maxWidth: {
    type: String,
    default: 'md', // 'sm' | 'md' | 'lg' | 'full'
  },
})

const emit = defineEmits(['update:modelValue', 'submit'])

const inputId = `search-${Math.random().toString(36).slice(2, 9)}`

const query = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})

const maxWidthClass = computed(() => {
  const map = {
    sm: 'max-w-xs',
    md: 'max-w-sm',
    lg: 'max-w-md',
    full: 'max-w-full',
  }
  return map[props.maxWidth] ?? map.md
})

function clear() {
  query.value = ''
}

function handleSubmit() {
  emit('submit', query.value)
}
</script>
