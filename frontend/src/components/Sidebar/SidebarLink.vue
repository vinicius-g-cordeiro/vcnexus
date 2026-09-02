<template>
  <a
    :href="href"
    :class="[
      'flex items-center gap-3 px-4 py-2.5 rounded-md text-sm font-medium transition-colors',
      'dark:text-neutral-50 text-neutral-900',
      active
        ? 'bg-emerald-500 text-white'
        : 'hover:bg-neutral-300 dark:hover:bg-neutral-700',
    ]"
  >
    <span v-if="$slots.icon" class="h-5 w-5 shrink-0">
      <slot name="icon" />
    </span>
    <span class="truncate"><slot /></span>
  </a>
</template>

<script setup>
/**
 * SidebarLink.vue — a single navigation item inside <Sidebar>.
 * Usage:
 * <SidebarLink href="/products" active>
 *   <template #icon><IconBox /></template>
 *   Products
 * </SidebarLink>
 */
defineProps({
  href: {
    type: String,
    default: '#',
  },
  active: {
    type: Boolean,
    default: false,
  },
})
</script>
