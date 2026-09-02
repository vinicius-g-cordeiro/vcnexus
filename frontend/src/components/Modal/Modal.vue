<template>
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="modelValue" class="z-[100] fixed inset-0 flex justify-center items-center p-4" @click.self="handleBackdropClick">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />

        <!-- Panel -->
        <Transition appear enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
          <div v-if="modelValue" :class="[
            'relative z-10 w-full rounded-xl shadow-2xl overflow-hidden flex flex-col',
            'dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900',
            'max-h-[90vh]',
            sizeClass,
          ]" role="dialog" aria-modal="true" :aria-label="title">
            <!-- Image / media mode (lightbox) -->
            <template v-if="image">
              <img :src="image" :alt="title" class="bg-neutral-900/5 dark:bg-neutral-900 w-full max-h-[80vh] object-contain" />
              <button type="button" class="top-3 right-3 absolute bg-black/50 hover:bg-black/70 p-2 rounded-full text-white transition-colors" aria-label="Close" @click="close">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
              <div v-if="title || $slots.caption" class="px-5 py-3 border-neutral-300 dark:border-neutral-700 border-t">
                <slot name="caption">
                  <p class="font-medium text-sm">{{ title }}</p>
                </slot>
              </div>
            </template>

            <!-- Standard content mode -->
            <template v-else>
              <div v-if="title || $slots.header" class="flex justify-between items-center gap-4 px-5 py-4 border-neutral-300 dark:border-neutral-700 border-b shrink-0">
                <slot name="header">
                  <h2 class="font-semibold text-base">{{ title }}</h2>
                </slot>
                <button type="button" class="hover:bg-neutral-300 dark:hover:bg-neutral-700 p-1.5 rounded-md transition-colors shrink-0" aria-label="Close" @click="close">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                  </svg>
                </button>
              </div>

              <div class="px-5 py-5 overflow-y-auto">
                <slot />
              </div>

              <div v-if="$slots.footer" class="flex justify-end items-center gap-3 px-5 py-4 border-neutral-300 dark:border-neutral-700 border-t shrink-0">
                <slot name="footer" />
              </div>
            </template>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
/**
 * Modal.vue — centered modal with backdrop blur + scale-in transition,
 * similar to lightbox/fancybox. Two modes:
 *  - pass `image` for a lightbox-style media viewer (with optional
 *    caption via #caption slot)
 *  - omit `image` for a standard content modal (#header, default,
 *    #footer slots)
 *
 * Teleports to <body> so it always sits above app layout/overflow.
 * Closes on backdrop click (unless `persistent`) and Escape.
 *
 * Usage (content modal):
 * <Modal v-model="isOpen" title="Edit profile" size="md">
 *   <Fieldset legend="Details">
 *     <BaseInput v-model="form.name" label="Name" />
 *   </Fieldset>
 *   <template #footer>
 *     <button @click="isOpen = false">Cancel</button>
 *     <button class="bg-emerald-500 text-white">Save</button>
 *   </template>
 * </Modal>
 *
 * Usage (lightbox):
 * <Modal v-model="isOpen" image="/photos/full.jpg" title="Sunset over the bay" />
 */
import { computed, watch, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
  image: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'md', // 'sm' | 'md' | 'lg' | 'xl' | 'full'
  },
  persistent: {
    type: Boolean,
    default: false, // if true, backdrop click won't close it
  },
  closeOnEscape: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['update:modelValue', 'close'])

const sizeClass = computed(() => {
  if (props.image) return 'max-w-3xl'
  const map = {
    sm: 'max-w-sm',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl',
    full: 'max-w-[95vw]',
  }
  return map[props.size] ?? map.md
})

function close() {
  emit('update:modelValue', false)
  emit('close')
}

function handleBackdropClick() {
  if (!props.persistent) close()
}

function handleEscape(e) {
  if (props.closeOnEscape && e.key === 'Escape' && props.modelValue) {
    close()
  }
}

// Lock body scroll while open, matching lightbox/fancybox behavior
watch(
  () => props.modelValue,
  (isOpen) => {
    if (typeof document === 'undefined') return
    document.body.style.overflow = isOpen ? 'hidden' : ''
  }
)

if (typeof window !== 'undefined') {
  window.addEventListener('keydown', handleEscape)
}

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleEscape)
  }
  if (typeof document !== 'undefined') {
    document.body.style.overflow = ''
  }
})
</script>
