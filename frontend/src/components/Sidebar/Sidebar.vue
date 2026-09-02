<template>
  <!-- Backdrop -->
  <Transition enter-active-class="transition-opacity duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
    <div v-if="isOpen" class="z-40 fixed inset-0 bg-black/50" @click="close" />
  </Transition>

  <!-- Offcanvas panel -->
  <Transition :enter-active-class="`transition-transform duration-300 ease-out`" :enter-from-class="placement === 'left' ? '-translate-x-full' : 'translate-x-full'" enter-to-class="translate-x-0" :leave-active-class="`transition-transform duration-200 ease-in`" leave-from-class="translate-x-0"
    :leave-to-class="placement === 'left' ? '-translate-x-full' : 'translate-x-full'">
    <aside v-if="isOpen" :class="[
      'fixed top-0 z-50 h-full w-72 max-w-[85vw] overflow-y-auto',
      'dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900',
      'shadow-xl flex flex-col',
      placement === 'left' ? 'left-0' : 'right-0',
    ]" role="dialog" aria-modal="true" :aria-label="ariaLabel">
      <div class="flex justify-between items-center px-4 py-4 border-neutral-300 dark:border-neutral-700 border-b">
        <slot name="header">
          <span class="font-semibold text-lg">Menu</span>
        </slot>
        <button type="button" class="hover:bg-neutral-300 dark:hover:bg-neutral-700 p-2 rounded-md transition-colors" aria-label="Close sidebar" @click="close">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 px-2 py-4">
        <slot />
      </nav>

      <div v-if="$slots.footer" class="px-4 py-4 border-neutral-300 dark:border-neutral-700 border-t">
        <slot name="footer" />
      </div>
    </aside>
  </Transition>
</template>

<script setup>
/**
 * Sidebar.vue — Offcanvas navigation drawer
 *
 * Usage:
 * <Sidebar v-model="sidebarOpen" placement="left">
 *   <SidebarLink href="/">Home</SidebarLink>
 *   <SidebarLink href="/about">About</SidebarLink>
 * </Sidebar>
 *
 * Pair with useSidebar() from sidebar.js for shared open/close state
 * across a SidebarToggle button placed anywhere (e.g. the header).
 */
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  placement: {
    type: String,
    default: 'left', // 'left' | 'right'
    validator: (v) => ['left', 'right'].includes(v),
  },
  ariaLabel: {
    type: String,
    default: 'Sidebar navigation',
  },
  closeOnEscape: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['update:modelValue'])

const isOpen = computed(() => props.modelValue)

function close() {
  emit('update:modelValue', false)
}

if (typeof window !== 'undefined') {
  window.addEventListener('keydown', (e) => {
    if (props.closeOnEscape && e.key === 'Escape' && isOpen.value) {
      close()
    }
  })
}
</script>
