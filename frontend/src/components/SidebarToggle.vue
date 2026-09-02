<template>
  <button
    type="button"
    :class="[
      'inline-flex items-center justify-center rounded-md p-2 transition-colors',
      'dark:text-zinc-50 text-zinc-900',
      'hover:bg-zinc-300 dark:hover:bg-zinc-700',
      'focus:outline-none focus:ring-2 focus:ring-emerald-500',
    ]"
    :aria-expanded="modelValue"
    aria-label="Toggle navigation menu"
    @click="$emit('update:modelValue', !modelValue)"
  >
    <!-- Hamburger / close morph icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line
        x1="4" y1="6" x2="20" y2="6"
        class="origin-center transition-transform duration-200"
        :class="modelValue ? 'translate-y-[6px] rotate-45' : ''"
      />
      <line
        x1="4" y1="12" x2="20" y2="12"
        class="transition-opacity duration-150"
        :class="modelValue ? 'opacity-0' : 'opacity-100'"
      />
      <line
        x1="4" y1="18" x2="20" y2="18"
        class="origin-center transition-transform duration-200"
        :class="modelValue ? '-translate-y-[6px] -rotate-45' : ''"
      />
    </svg>
  </button>
</template>

<script setup>
/**
 * SidebarToggle.vue — icon button that opens/closes a Sidebar.
 * Place anywhere (e.g. in the Header) and bind to the same v-model
 * used by <Sidebar>.
 *
 * Usage:
 * <SidebarToggle v-model="sidebarOpen" />
 */
defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue'])
</script>
