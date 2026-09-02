<template>
  <button
    type="button"
    :class="[
      'inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors',
      'dark:text-neutral-50 text-neutral-900',
      'hover:bg-neutral-300 dark:hover:bg-neutral-700',
      'focus:outline-none focus:ring-2 focus:ring-emerald-500',
    ]"
    aria-haspopup="true"
  >
    <slot />
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="6 9 12 15 18 9" />
    </svg>
  </button>
</template>

<script setup>
/**
 * DropdownButton.vue — the clickable trigger for a Dropdown.
 * Content passed via default slot (e.g. avatar + name).
 * Emits a native `click` — bind it to the `toggle` fn provided
 * by <Dropdown>'s #trigger slot.
 */
</script>
