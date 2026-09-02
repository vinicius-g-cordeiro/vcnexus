<template>
  <form role="search" :class="['relative w-full', maxWidthClass]" @submit.prevent="handleSubmit">
    <label :for="inputId" class="sr-only">{{ t('header.searchbar') }}</label>

    <svg xmlns="http://www.w3.org/2000/svg" class="top-1/2 left-3 absolute w-4 h-4 text-zinc-500 dark:text-zinc-400 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="11" cy="11" r="8" />
      <line x1="21" y1="21" x2="16.65" y2="16.65" />
    </svg>

    <input :id="inputId" v-model="query" type="search" :placeholder="t('header.searchbar')" :class="[
      'w-full rounded-md py-2 pl-9 pr-9 text-sm transition-colors',
      'dark:bg-zinc-800 bg-neutral-100 dark:text-zinc-50 text-zinc-900',
      'placeholder:text-zinc-500 dark:placeholder:text-zinc-400',
      'border border-emerald-500/75 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500',
    ]" />

    <button v-if="query" type="button" class="top-1/2 right-2.5 absolute text-zinc-500 hover:text-emerald-500 dark:text-zinc-400 -translate-y-1/2" aria-label="Clear search" @click="clear">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

import { useI18n } from 'vue-i18n'
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

const { t } = useI18n()
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
