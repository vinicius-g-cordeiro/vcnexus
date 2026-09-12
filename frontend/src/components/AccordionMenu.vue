<template>
  <ul class="m-0 p-0 w-full list-none" :class="{ 'pl-2': depth > 0 }">
    <li v-for="(item, index) in items" :key="item.href ?? item.label ?? index" class="w-full">
      <!-- Leaf link -->
      <RouterLink v-if="!hasChildren(item)" :to="item.href" class="flex items-center gap-2.5 hover:bg-emerald-500 px-3 py-2.5 w-full text-zinc-900 dark:text-zinc-200 text-sm transition-colors" active-class="border-b border-b-emerald-500 text-emerald-600 font-semibold hover:bg-emerald-50" @click="$emit('navigate', item)"
        :title="item.title || item.label">
        <i v-if="item.icon" :class="item.icon" class="w-5 text-center shrink-0"></i>
        <span class="flex-1 text-left truncate">{{ item.label }}</span>
      </RouterLink>

      <!-- Parent with children -->
      <template v-else>
        <button type="button" class="flex items-center hover:bg-emerald-500 px-3 py-2.5 w-full text-zinc-900 dark:text-zinc-200 text-sm transition-colors cursor-pointer" :aria-expanded="isOpen(index)" @click="toggle(index)">
          <i v-if="item.icon" :class="item.icon" class="w-5 text-center shrink-0"></i>
          <span class="flex-1 text-left truncate">{{ item.label }}</span>
          <i class="text-xs transition-transform duration-200 bi bi-chevron-down shrink-0" :class="{ 'rotate-180': isOpen(index) }"></i>
        </button>

        <transition enter-active-class="transition-[grid-template-rows] duration-200 ease-out" leave-active-class="transition-[grid-template-rows] duration-200 ease-in" enter-from-class="grid-rows-[0fr]" enter-to-class="grid-rows-[1fr]" leave-from-class="grid-rows-[1fr]"
          leave-to-class="grid-rows-[0fr]">
          <div v-if="isOpen(index)" class="grid overflow-hidden">
            <div class="min-h-0">
              <AccordionMenu :items="item.children" :depth="depth + 1" @navigate="$emit('navigate', $event)" />
            </div>
          </div>
        </transition>
      </template>
    </li>
  </ul>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  depth: {
    type: Number,
    default: 0,
  },
  // When true, opening one top-level item closes the others (classic accordion behaviour)
  singleOpen: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['navigate'])

const openIndexes = ref(new Set())

function hasChildren(item) {
  return Array.isArray(item.children) && item.children.length > 0
}

function isOpen(index) {
  return openIndexes.value.has(index)
}

function toggle(index) {
  const next = new Set(props.singleOpen ? [] : openIndexes.value)
  if (openIndexes.value.has(index)) {
    next.delete(index)
  } else {
    next.add(index)
  }
  openIndexes.value = next
}

// Reset open state if the items list itself changes (e.g. role switch)
watch(
  () => props.items,
  () => {
    openIndexes.value = new Set()
  }
)
</script>