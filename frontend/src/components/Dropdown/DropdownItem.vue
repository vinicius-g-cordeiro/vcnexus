<template>
  <span v-if="!deactivated">
    <router-link v-if="redirectTo && !href" :to="redirectTo" role="menuitem" :class="noDecoration ? ['flex items-center gap-2 px-4 py-2 text-sm transition-colors',
      'dark:text-neutral-50 text-neutral-900 hover:bg-neutral-200'] : [
      'flex items-center gap-2 px-4 py-2 text-sm transition-colors',
      'dark:text-neutral-50 text-neutral-900',
      'hover:bg-emerald-500 hover:text-white',
    ]"  @click="handleClick">
      <span v-if="$slots.icon" class="w-4 h-4 shrink-0">
        <slot name="icon" />
      </span>
      <slot />
    </router-link>
    <a v-if="href" :href="href" role="menuitem" :class="noDecoration ? ['flex items-center gap-2 px-4 py-2 text-sm transition-colors',
      'dark:text-neutral-50 text-neutral-900 hover:bg-neutral-200'] : [
      'flex items-center gap-2 px-4 py-2 text-sm transition-colors',
      'dark:text-neutral-50 text-neutral-900',
      'hover:bg-emerald-500 hover:text-white',
    ]"  @click="handleClick">
      <span v-if="$slots.icon" class="w-4 h-4 shrink-0">
        <slot name="icon" />
      </span>
      <slot />
    </a>
  </span>
  <span v-else>
    <a href="#" role="menuitem" :class="noDecoration ? ['flex items-center gap-2 px-4 py-2 text-sm transition-colors',
      'dark:text-neutral-50 text-neutral-900 hover:bg-neutral-200'] : [
      'flex items-center gap-2 px-4 py-2 text-sm transition-colors',
      'dark:text-neutral-50 text-neutral-900',
      'hover:bg-emerald-500 hover:text-white',
    ]"  @click="handleClick">
      <span v-if="$slots.icon" class="w-4 h-4 shrink-0">
        <slot name="icon" />
      </span>
      <slot />
    </a>
  </span>
</template>

<script setup>
/**
 * DropdownItem.vue — a single option/link inside a Dropdown.
 * Works as a router-link target: pass `href` (or swap the <a> for
 * <router-link :to="..."> if using vue-router).
 * Automatically closes the parent dropdown on click when used
 * inside the default slot of <Dropdown>, via the injected `close`.
 */
const emit = defineEmits(['click'])

defineProps({
  href: {
    type: String,
  },
  redirectTo: {
    type: Object,
  },
  deactivated: {
    type: String
  },
  noDecoration: {
    type: String
  }
})

function handleClick(e) {
  emit('click', e)
}
</script>
