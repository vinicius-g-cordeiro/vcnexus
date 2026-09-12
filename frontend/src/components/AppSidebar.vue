<template>
  <nav>
    <!-- ============ DESKTOP RAIL ============ -->
    <aside :class="[
      'hidden lg:flex flex-col shrink-0 h-screen sticky top-0 z-20',
      'dark:bg-zinc-800 bg-zinc-100 dark:text-zinc-50 text-zinc-900',
      'border-r dark:border-zinc-700 border-zinc-300',
      'transition-[width] duration-200 ease-out',
      collapsed ? 'w-16' : 'w-64',
    ]">
      <!-- Brand + collapse toggle -->
      <div class="flex items-center gap-2 px-3 h-16 shrink-0" :class="collapsed ? 'justify-center' : 'justify-between'">
        <a v-if="!collapsed" href="/" class="font-semibold text-lg truncate tracking-tight">
          <slot name="brand">VCNexus</slot>
        </a>
        <button type="button" class="hover:bg-zinc-300 dark:hover:bg-zinc-700 p-2 rounded-md transition-colors shrink-0" :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'" @click="toggleCollapsed">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <line x1="9" y1="4" x2="9" y2="20" />
          </svg>
        </button>
      </div>

      <!-- Nav links -->
      <nav class="flex flex-col gap-0.5 px-2" v-if="navItems.length">
        <AccordionMenu :items="navItems" />
      </nav>

      <div v-if="navItems.length && listItems.length" class="mx-3 my-3 border-zinc-300 dark:border-zinc-700 border-t" />

      <!-- Secondary list section (e.g. history / recents / projects) -->
      <div class="flex-1 px-2 pb-2 overflow-y-auto" v-if="listItems.length">
        <p v-if="!collapsed" class="px-2 pt-1 pb-1.5 font-medium text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wide">
          {{ listLabel }}
        </p>
        <a v-for="item in listItems" :key="item.id ?? item.label" :href="item.href" :title="collapsed ? item.label : undefined" :class="[
          'group flex items-center gap-2 rounded-md text-sm transition-colors truncate',
          'hover:bg-zinc-300 dark:hover:bg-zinc-700',
          collapsed ? 'justify-center p-2.5' : 'px-2.5 py-2',
          item.active ? 'bg-zinc-300 dark:bg-zinc-700 font-medium' : '',
        ]">
          <svg v-if="collapsed" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
          </svg>
          <span v-else class="truncate">{{ item.label }}</span>
        </a>
      </div>
      <div v-else class="flex-1" />

      <!-- Footer (e.g. profile) -->
      <div v-if="$slots.footer" class="px-2 py-3 border-zinc-300 dark:border-zinc-700 border-t">
        <slot name="footer" :collapsed="collapsed" />
      </div>
    </aside>


    <!-- ============ MOBILE OFFCANVAS ============ -->
    <Sidebar v-model="mobileOpen" placement="left" :aria-label="ariaLabel">
      <template #header>
        <span class="font-semibold text-lg">
          <slot name="brand">VCNexus</slot>
        </span>
      </template>

      <div class="px-2 pb-2">
        <button type="button" class="flex items-center gap-2 bg-emerald-500 hover:opacity-90 px-3 py-2.5 rounded-md w-full font-medium text-zinc-100 text-sm transition-colors" @click="$emit('new-item')">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          {{ newItemLabel }}
        </button>
      </div>

      <SidebarLink v-for="item in navItems" :key="item.label" :href="item.href">
        <i v-if="item.icon" :class="[item.icon, 'mr-2']"></i>
        {{ item.label }}
      </SidebarLink>

      <div v-if="listItems.length" class="mt-3">
        <p class="px-3 pt-1 pb-1.5 font-medium text-zinc-500 dark:text-zinc-400 text-xs uppercase tracking-wide">
          {{ listLabel }}
        </p>
        <SidebarLink v-for="item in listItems" :key="item.id ?? item.label" :href="item.href" :class="item.active ? 'bg-zinc-300 dark:bg-zinc-700 font-medium' : ''">
          {{ item.label }}
        </SidebarLink>
      </div>

      <template #footer v-if="$slots.footer">
        <slot name="footer" :collapsed="false" />
      </template>
    </Sidebar>

  </nav>
</template>

<script setup>
/**
 * AppSidebar.vue — claude.ai-style application sidebar.
 *
 * Desktop (lg+): persistent rail, collapsible to icon-only width (w-16 <-> w-64).
 * Mobile (<lg): offcanvas drawer (reuses existing Sidebar.vue), triggered via
 * v-model:mobileOpen or SidebarToggle in the Header.
 *
 * Usage:
 * <AppSidebar
 *   v-model:mobileOpen="isSidebarOpen"
 *   v-model:collapsed="isSidebarCollapsed"
 *   :nav-items="[{ label: 'Dashboard', href: '/', icon: 'bi bi-house' }]"
 *   :list-items="conversations"
 *   list-label="Recent"
 *   new-item-label="New chat"
 *   @new-item="createNewChat"
 * >
 *   <template #brand>VCNexus</template>
 *   <template #footer="{ collapsed }">...profile...</template>
 * </AppSidebar>
 *
 * Persist `collapsed` state yourself (e.g. localStorage) if you want it to
 * survive reloads — this component only manages it as v-model state.
 */
import { computed } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import SidebarLink from '@/components/SidebarLink.vue'
import SidebarRailLink from '@/components/SidebarRailLink.vue'
import AccordionMenu from '@/components/AccordionMenu.vue'

const props = defineProps({
  mobileOpen: {
    type: Boolean,
    default: false,
  },
  collapsed: {
    type: Boolean,
    default: false,
  },
  navItems: {
    type: Array,
    default: () => [],
    // [{ label, href, icon }]
  },
  listItems: {
    type: Array,
    default: () => [],
    // [{ id, label, href, icon, active }]
  },
  listLabel: {
    type: String,
    default: 'Recent',
  },
  newItemLabel: {
    type: String,
    default: 'New',
  },
  ariaLabel: {
    type: String,
    default: 'Sidebar navigation',
  },
})

const emit = defineEmits(['update:mobileOpen', 'update:collapsed', 'new-item'])

const mobileOpen = computed({
  get: () => props.mobileOpen,
  set: (v) => emit('update:mobileOpen', v),
})

function toggleCollapsed() {
  emit('update:collapsed', !props.collapsed)
}
</script>
