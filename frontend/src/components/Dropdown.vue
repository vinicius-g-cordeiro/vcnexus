<template>
  <div ref="rootEl" class="inline-block relative text-left">
    <slot name="trigger" :toggle="toggle" :isOpen="isOpen" />

    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 scale-95 -translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-1"
    >
      <div
        v-if="isOpen"
        :class="[
          'absolute z-50 min-w-[12rem] rounded-lg shadow-lg py-1 origin-top',
          'dark:bg-zinc-800 bg-neutral-100 dark:text-zinc-50 text-zinc-900',
          'ring-1 ring-black/5',
          alignClass,
        ]"
        :style="offsetStyle"
        role="menu"
      >
        <slot :close="close" />
      </div>
    </Transition>
  </div>
</template>

<script setup>
/**
 * Dropdown.vue — generic animated dropdown wrapper.
 * Use DropdownButton for the trigger content and DropdownItem for
 * each option, or pass your own trigger via the #trigger slot.
 *
 * Usage:
 * <Dropdown align="right" :offset="{ x: 0, y: 8 }">
 *   <template #trigger="{ toggle }">
 *     <DropdownButton @click="toggle">Account</DropdownButton>
 *   </template>
 *   <DropdownItem href="/profile">Profile</DropdownItem>
 *   <DropdownItem href="/logout">Log out</DropdownItem>
 * </Dropdown>
 */
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  align: {
    type: String,
    default: 'left', // 'left' | 'right'
    validator: (v) => ['left', 'right'].includes(v),
  },
  offset: {
    type: Object,
    default: () => ({ x: 0, y: 8 }), // px
  },
  closeOnClickOutside: {
    type: Boolean,
    default: true,
  },
})

const isOpen = ref(false)
const rootEl = ref(null)

const alignClass = computed(() =>
  props.align === 'right' ? 'right-0' : 'left-0'
)

const offsetStyle = computed(() => ({
  marginTop: `${props.offset.y ?? 8}px`,
  marginLeft: `${props.offset.x ?? 0}px`,
}))

function toggle() {
  isOpen.value = !isOpen.value
}

function close() {
  isOpen.value = false
}

function handleClickOutside(e) {
  if (!props.closeOnClickOutside) return
  if (rootEl.value && !rootEl.value.contains(e.target)) {
    close()
  }
}

function handleEscape(e) {
  if (e.key === 'Escape') close()
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscape)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscape)
})

defineExpose({ isOpen, toggle, close })
</script>
