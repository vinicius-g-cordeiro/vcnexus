<template>
  <header :class="[
    'sticky top-0 z-30 w-full border-b',
    'dark:bg-zinc-800 bg-neutral-100 dark:border-neutral-700 border-neutral-300',
    'dark:text-zinc-50 text-zinc-900',
  ]">
    <div class="flex items-center gap-3 mx-auto px-4 max-w-7xl h-16">
      <!-- Mobile: sidebar toggle -->
      <SidebarToggle v-model="isSidebarOpen" class="lg:hidden" />

      <!-- Brand -->
      <a href="/" class="font-semibold text-lg tracking-tight shrink-0">
        <slot name="brand">VCNexus</slot>
      </a>

      <!-- Desktop nav -->
      <nav class="hidden lg:flex items-center gap-1 ml-6">
        <a v-for="item in navItems.filter((i) => !i.children)" :key="item.label" :href="item.href" class="justify-between hover:bg-zinc-300 dark:hover:bg-zinc-700 px-3 py-2 rounded-md font-medium text-sm align-middle transition-colors">
          <slot name="icon" v-if="item.icon">
              <i :class="item.icon"></i>
          </slot>
          {{ item.label }}
        </a>

        <!-- Submenu dropdown, e.g. Products -> [item1, item2, item3] -->
        <Dropdown v-for="item in navItems.filter((i) => i.children)" :key="item.label">
          <template #trigger="{ toggle }">
            <button type="button" class="inline-flex items-center gap-1 hover:bg-zinc-300 dark:hover:bg-zinc-700 px-3 py-2 rounded-md font-medium text-sm transition-colors" @click="toggle">
              <slot name="icon" v-if="item.icon">
                <i :class="item.icon"></i>
              </slot>
              {{ item.label }}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9" />
              </svg>
            </button>
          </template>
          <DropdownItem v-for="child in item.children" :key="child.label" :href="child.href">
              <slot name="icon" v-if="child.icon">
                <i :class="child.icon"></i>
            </slot>
            {{ child.label }}
          </DropdownItem>
        </Dropdown>
      </nav>

      <div class="flex-1" />

      <!-- Search (desktop) -->
      <SearchBar v-model="searchQuery" class="hidden md:block" max-width="sm" @submit="$emit('search', $event)" />

      
      <LanguageDropdown/>
      <!-- Dark mode -->
      <DarkModeButton />


      <!-- Profile dropdown -->
      <Dropdown align="right" v-if="authStore.sessionUser">
        <template #trigger="{ toggle }">
          <button type="button" class="flex justify-center items-center bg-emerald-500 hover:opacity-90 rounded-full w-9 h-9 font-medium text-zinc-100 text-sm transition-opacity" aria-label="Open user menu" @click="toggle">
            <slot name="avatar">{{ userInitials }}</slot>
          </button>
        </template>

        <DropdownItem deactivated="true" noDecoration="true">{{ authStore.sessionUser.name }} {{ authStore.sessionUser.surname }} {{ authStore.sessionUser.lastname }}</DropdownItem>
        <DropdownItem deactivated="true" noDecoration="true">{{ authStore.sessionUser.email }}</DropdownItem>
        <DropdownItem href="/profile">
          <template v-slot:icon>
            <i class="bi bi-person-fill"></i>
          </template>
          {{ t('header.dropdown.profile') }}
        </DropdownItem>
        <DropdownItem href="/settings">
          <template v-slot:icon> <i class="bi bi-gear-fill"></i></template>
          {{ t('header.dropdown.settings') }}
        </DropdownItem>
        <DropdownItem deactivated="true" @click="handleLogout">
          <template v-slot:icon> <i class="bi-box-arrow-left"></i></template>
          {{ t('header.dropdown.logout') }}
        </DropdownItem>
      </Dropdown>


      <!-- Profile dropdown -->
      <Dropdown align="right" v-if="!authStore.sessionUser">
        <template #trigger="{ toggle }">
          <button type="button" class="flex justify-center items-center bg-emerald-500 hover:opacity-90 rounded-full w-9 h-9 font-medium text-zinc-100 text-sm transition-opacity" aria-label="Open user menu" @click="toggle">
            <slot name="avatar">{{ "U" }}</slot>
          </button>
        </template>
        <DropdownItem :redirectTo=loginPage>
          <template v-slot:icon> <i class="bi bi-door-open-fill"></i></template>
          Login
        </DropdownItem>
        <DropdownItem :redirectTo=registerPage>
          <template v-slot:icon> <i class="bi bi-person-plus-fill"></i></template>
          Register</DropdownItem>
      </Dropdown>

    </div>


    <!-- Mobile offcanvas sidebar -->
    <Sidebar v-model="isSidebarOpen" placement="left">
      <SidebarLink v-for="item in flatNavItems" :key="item.label" :href="item.href">
        {{ item.label }}
      </SidebarLink>
    </Sidebar>
  </header>
</template>

<script setup>
/**
 * Header.vue — top navigation bar composing Sidebar, Dropdown,
 * DarkModeButton, and SearchBar.
 *
 * Usage:
 * <Header
 *   :nav-items="[
 *     { label: 'Home', href: '/' },
 *     { label: 'Products', children: [
 *       { label: 'Item 1', href: '/products/1' },
 *       { label: 'Item 2', href: '/products/2' },
 *     ]},
 *   ]"
 *   user-name="Jane Doe"
 *   @search="onSearch"
 * >
 *   <template #brand>Acme</template>
 * </Header>
 */
import { ref, computed } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import SidebarToggle from '@/components/SidebarToggle.vue'
import SidebarLink from '@/components/SidebarLink.vue'
import Dropdown from '@/components/Dropdown.vue'
import DropdownItem from '@/components/DropdownItem.vue'
import DarkModeButton from '@/components/DarkModeButton.vue'
import SearchBar from '@/components/SearchBar.vue'
import LanguageDropdown from '@/components/LanguageDropdown.vue'

import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from "@/stores/authStore"
import { useI18n } from 'vue-i18n'


const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const props = defineProps({
  navItems: {
    type: Array,
    default: () => [],
    // [{ label, href }] or [{ label, children: [{ label, href }] }]
  },
  userName: {
    type: String,
    default: 'U',
  },
})

defineEmits(['search'])

const isSidebarOpen = ref(false)
const searchQuery = ref('')
const loginPage = ref({
  name: 'login'
});

const registerPage = ref({
  name: 'register'
});

const flatNavItems = computed(() =>
  props.navItems.flatMap((item) => (item.children ? item.children : [item]))
)

const userInitials = computed(() =>
  (authStore.sessionUser.name)
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
  +
  (authStore.sessionUser.lastname)
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)

async function handleLogout() {
  const ok = await authStore.logout()
  if (ok) {
    router.push({ name: 'login' })
  }
}
</script>
