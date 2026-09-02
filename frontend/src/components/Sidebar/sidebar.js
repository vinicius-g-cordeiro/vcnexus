// sidebar.js
// Shared reactive state so a SidebarToggle in the Header and the
// Sidebar itself stay in sync without prop-drilling.
//
// Usage:
//   import { useSidebar } from './sidebar.js'
//   const { isOpen, open, close, toggle } = useSidebar()
//
//   <SidebarToggle v-model="isOpen" />
//   <Sidebar v-model="isOpen">...</Sidebar>

import { ref } from 'vue'

const isOpen = ref(false)

export function useSidebar() {
  function open() {
    isOpen.value = true
  }

  function close() {
    isOpen.value = false
  }

  function toggle() {
    isOpen.value = !isOpen.value
  }

  return { isOpen, open, close, toggle }
}
