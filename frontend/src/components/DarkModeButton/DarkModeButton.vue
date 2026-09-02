<template>
  <button
    type="button"
    :class="[
      'relative inline-flex items-center justify-center rounded-full p-2 overflow-hidden transition-colors',
      'dark:text-gold text-emerald-500',
      'hover:bg-neutral-300 dark:hover:bg-neutral-700',
      'focus:outline-none focus:ring-2 focus:ring-emerald-500',
    ]"
    :aria-pressed="isDark"
    aria-label="Toggle dark mode"
    @click="toggle"
  >
    <!-- Sun -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5 transition-all duration-300 ease-out"
      :class="isDark ? 'scale-0 -rotate-90 opacity-0 absolute' : 'scale-100 rotate-0 opacity-100'"
      viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <circle cx="12" cy="12" r="4" />
      <line x1="12" y1="2" x2="12" y2="4" />
      <line x1="12" y1="20" x2="12" y2="22" />
      <line x1="4.93" y1="4.93" x2="6.34" y2="6.34" />
      <line x1="17.66" y1="17.66" x2="19.07" y2="19.07" />
      <line x1="2" y1="12" x2="4" y2="12" />
      <line x1="20" y1="12" x2="22" y2="12" />
      <line x1="4.93" y1="19.07" x2="6.34" y2="17.66" />
      <line x1="17.66" y1="6.34" x2="19.07" y2="4.93" />
    </svg>

    <!-- Moon -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5 transition-all duration-300 ease-out"
      :class="isDark ? 'scale-100 rotate-0 opacity-100' : 'scale-0 rotate-90 opacity-0 absolute'"
      viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
    </svg>
  </button>
</template>

<script setup>
/**
 * DarkModeButton.vue — toggles the `dark` class on <html>.
 * Pair with darkmode.js for the persisted/system-aware composable.
 *
 * Usage:
 * <DarkModeButton />
 */
import { useDarkMode } from './darkmode.js'

const { isDark, toggle } = useDarkMode()
</script>
