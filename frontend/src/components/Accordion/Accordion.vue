<template>
  <div class="flex flex-col gap-2">
    <div
      v-for="(item, index) in items"
      :key="item.id ?? index"
      :class="[
        'rounded-lg overflow-hidden border transition-colors',
        'dark:bg-neutral-800 bg-neutral-200 dark:border-neutral-700 border-neutral-300',
      ]"
    >
      <button
        type="button"
        :class="[
          'w-full flex items-center justify-between gap-4 px-4 py-3.5 text-left text-sm font-medium transition-colors',
          'dark:text-neutral-50 text-neutral-900',
          'hover:bg-neutral-300/50 dark:hover:bg-neutral-700/50',
        ]"
        :aria-expanded="isOpen(index)"
        @click="toggle(index)"
      >
        <span class="flex items-center gap-3">
          <span v-if="item.icon" class="h-5 w-5 shrink-0 text-emerald-500" v-html="item.icon" />
          {{ item.title }}
        </span>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 shrink-0 transition-transform duration-200 dark:text-neutral-400 text-neutral-500"
          :class="isOpen(index) ? 'rotate-180' : 'rotate-0'"
          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        >
          <polyline points="6 9 12 15 18 9" />
        </svg>
      </button>

      <div
        class="grid transition-[grid-template-rows] duration-300 ease-in-out"
        :style="{ gridTemplateRows: isOpen(index) ? '1fr' : '0fr' }"
      >
        <div class="overflow-hidden">
          <div class="px-4 pb-4 pt-0.5 text-sm dark:text-neutral-300 text-neutral-700">
            <!-- content can be plain text on the item, or rich content via the named slot -->
            <slot :name="`item-${item.id ?? index}`" :item="item">
              {{ item.content }}
            </slot>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * Accordion.vue — items are passed in dynamically as an array.
 * Content can be plain text (item.content) or, for rich/complex
 * content, a named slot `#item-<id>` (or `#item-<index>` if no id).
 *
 * Usage:
 * <Accordion
 *   :items="[
 *     { id: 'shipping', title: 'Shipping', content: 'Ships in 2-3 days.' },
 *     { id: 'returns', title: 'Returns', content: '30-day return window.' },
 *   ]"
 * />
 *
 * With rich content:
 * <Accordion :items="faqItems">
 *   <template #item-shipping="{ item }">
 *     <p>{{ item.content }}</p>
 *     <a href="/shipping-policy" class="text-emerald-500">Full policy</a>
 *   </template>
 * </Accordion>
 */
import { ref } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    required: true, // [{ id?, title, content?, icon? (raw svg string) }]
  },
  multiple: {
    type: Boolean,
    default: false, // allow more than one item open at once
  },
  defaultOpen: {
    type: [Number, Array],
    default: () => [], // index or array of indices open on mount
  },
})

const openIndices = ref(
  new Set(Array.isArray(props.defaultOpen) ? props.defaultOpen : [props.defaultOpen])
)

function isOpen(index) {
  return openIndices.value.has(index)
}

function toggle(index) {
  const next = new Set(props.multiple ? openIndices.value : [])
  if (openIndices.value.has(index)) {
    next.delete(index)
  } else {
    next.add(index)
  }
  openIndices.value = next
}
</script>
