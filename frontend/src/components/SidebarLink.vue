<template>
  <a
    :href="href"
    :class="[
      'flex items-center px-3 py-2.5 rounded-md text-sm font-medium transition-colors',
      'hover:bg-zinc-300 dark:hover:bg-zinc-700',
    ]"
  >
    <slot />
  </a>
</template>

<script setup>
defineProps({
  href: { type: String, default: '#' },
})
</script>
