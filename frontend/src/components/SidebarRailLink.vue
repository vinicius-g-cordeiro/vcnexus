<template>
  <a
    :href="href"
    :title="collapsed ? label : undefined"
    :class="[
      'flex items-center gap-3 rounded-md text-sm font-medium transition-colors',
      'hover:bg-zinc-300 dark:hover:bg-zinc-700',
      collapsed ? 'justify-center p-2.5' : 'px-2.5 py-2',
      active ? 'bg-zinc-300 dark:bg-zinc-700' : '',
    ]"
  >
    <i v-if="icon" :class="[icon, 'shrink-0 text-base leading-none']"></i>
    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="3" />
    </svg>
    <span v-if="!collapsed" class="truncate">{{ label }}</span>
  </a>
</template>

<script setup>
/**
 * SidebarRailLink.vue — single nav item for AppSidebar's desktop rail.
 * Shows icon + label expanded, icon-only (with title tooltip) when collapsed.
 */
defineProps({
  href: { type: String, default: '#' },
  label: { type: String, required: true },
  icon: { type: String, default: '' }, // e.g. 'bi bi-house-fill'
  collapsed: { type: Boolean, default: false },
  active: { type: Boolean, default: false },
})
</script>
