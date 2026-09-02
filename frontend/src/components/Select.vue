<template>
  <div ref="rootEl" class="relative w-full">
    <label v-if="label" :for="triggerId" class="block mb-1.5 font-medium text-zinc-900 dark:text-zinc-50 text-sm">
      {{ label }}
      <span v-if="required" class="text-emerald-500">*</span>
    </label>

    <!-- Trigger / control -->
    <button
      :id="triggerId"
      type="button"
      :disabled="disabled"
      :class="[
        'relative w-full min-h-[38px] rounded-md pl-3 pr-9 py-1.5 text-left text-sm transition-colors',
        'dark:bg-zinc-800 bg-neutral-100 dark:text-zinc-50 text-zinc-900',
        'border focus:outline-none',
        hasError
          ? 'border-red-500 focus:ring-1 focus:ring-red-500'
          : isOpen
            ? 'border-emerald-500 ring-1 ring-emerald-500'
            : 'border-zinc-500/90',
        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
      ]"
      @click="handleTriggerClick"
    >
      <div class="flex flex-wrap items-center gap-1.5">
        <!-- Multi-select: tags -->
        <template v-if="multiple">
          <span
            v-for="opt in selectedOptions"
            :key="opt.value"
            class="inline-flex items-center gap-1 bg-emerald-500/10 py-0.5 pr-1 pl-2 rounded font-medium text-emerald-500 text-xs"
          >
            {{ opt.label }}
            <button
              type="button"
              class="hover:bg-emerald-500/20 p-0.5 rounded transition-colors"
              :aria-label="`Remove ${opt.label}`"
              @click.stop="removeValue(opt.value)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </span>
          <span v-if="!selectedOptions.length" class="py-0.5 text-zinc-500 dark:text-zinc-400 text-sm">
            {{ placeholder }}
          </span>
        </template>

        <!-- Single-select: plain label -->
        <template v-else>
          <span v-if="selectedOptions[0]" class="py-0.5 text-sm truncate">{{ selectedOptions[0].label }}</span>
          <span v-else class="py-0.5 text-zinc-500 dark:text-zinc-400 text-sm">{{ placeholder }}</span>
        </template>
      </div>

      <!-- Clear + chevron -->
      <span class="top-0 right-2.5 bottom-0 absolute flex items-center gap-1">
        <button
          v-if="clearable && hasSelection && !disabled"
          type="button"
          class="p-0.5 text-zinc-500 hover:text-emerald-500 dark:text-zinc-400"
          aria-label="Clear selection"
          @click.stop="clearAll"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-4 h-4 text-zinc-500 dark:text-zinc-400 transition-transform duration-200"
          :class="isOpen ? 'rotate-180' : 'rotate-0'"
          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        >
          <polyline points="6 9 12 15 18 9" />
        </svg>
      </span>
    </button>

    <!-- Dropdown panel -->
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
        class="z-50 absolute bg-zinc-100 dark:bg-zinc-800 shadow-lg mt-1.5 rounded-lg ring-1 ring-black/5 w-full overflow-hidden origin-top"
      >
        <!-- Search box -->
        <div v-if="searchable" class="p-2 border-zinc-300 dark:border-zinc-700 border-b">
          <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="top-1/2 left-2.5 absolute w-4 h-4 text-zinc-500 dark:text-zinc-400 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input
              ref="searchEl"
              v-model="searchTerm"
              type="text"
              :placeholder="searchPlaceholder"
              class="bg-zinc-100 dark:bg-zinc-700 py-1.5 pr-2 pl-8 border border-transparent focus:border-emerald-500 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 w-full text-zinc-900 dark:placeholder:text-zinc-400 dark:text-zinc-50 placeholder:text-zinc-500 text-sm"
              @keydown="handleSearchKeydown"
            />
          </div>
        </div>

        <!-- Options list -->
        <ul
          ref="listEl"
          role="listbox"
          :aria-multiselectable="multiple"
          class="py-1 max-h-60 overflow-y-auto"
        >
          <li
            v-for="(opt, index) in filteredOptions"
            :key="opt.value"
            role="option"
            :aria-selected="isSelected(opt.value)"
            :class="[
              'flex items-center justify-between gap-2 px-3 py-2 text-sm cursor-pointer transition-colors',
              index === highlightedIndex ? 'bg-emerald-500 text-white' : 'dark:text-zinc-50 text-zinc-900 hover:bg-zinc-300 dark:hover:bg-zinc-700',
              isSelected(opt.value) && index !== highlightedIndex ? 'font-medium text-emerald-500' : '',
            ]"
            @click="selectOption(opt)"
            @mouseenter="highlightedIndex = index"
          >
            <span class="flex items-center gap-2 min-w-0">
              <span v-if="opt.icon" class="shrink-0" v-html="opt.icon" />
              <span class="truncate">{{ opt.label }}</span>
            </span>
            <svg
              v-if="isSelected(opt.value)"
              xmlns="http://www.w3.org/2000/svg"
              class="w-4 h-4 shrink-0"
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
            >
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </li>

          <li v-if="!filteredOptions.length" class="px-3 py-4 text-zinc-500 dark:text-zinc-400 text-sm text-center">
            {{ noResultsText }}
          </li>
        </ul>
      </div>
    </Transition>

    <p v-if="hasError" class="mt-1.5 text-red-500 text-xs">{{ error }}</p>
    <p v-else-if="hint" class="mt-1.5 text-zinc-500 dark:text-zinc-400 text-xs">{{ hint }}</p>
  </div>
</template>

<script setup>
/**
 * Select.vue — Select2-style searchable dropdown select.
 * Supports single or multiple selection, live search filtering,
 * removable tags (multi mode), clear-all, and full keyboard nav
 * (Up/Down to highlight, Enter to choose, Escape to close).
 *
 * Usage (single):
 * <Select
 *   v-model="form.country"
 *   label="Country"
 *   placeholder="Select a country..."
 *   :options="[{ label: 'United States', value: 'us' }, { label: 'Brazil', value: 'br' }]"
 * />
 *
 * Usage (multi, with tags):
 * <Select
 *   v-model="form.tags"
 *   multiple
 *   label="Tags"
 *   placeholder="Add tags..."
 *   :options="tagOptions"
 * />
 */
import { ref, computed, nextTick, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    default: null,
  },
  options: {
    type: Array,
    required: true, // [{ label, value, icon? (raw svg string) }]
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  searchable: {
    type: Boolean,
    default: true,
  },
  clearable: {
    type: Boolean,
    default: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  label: { type: String, default: '' },
  placeholder: { type: String, default: 'Select...' },
  searchPlaceholder: { type: String, default: 'Search...' },
  noResultsText: { type: String, default: 'No results found' },
  hint: { type: String, default: '' },
  error: { type: String, default: '' },
  required: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'open', 'close'])

const rootEl = ref(null)
const searchEl = ref(null)
const listEl = ref(null)
const isOpen = ref(false)
const searchTerm = ref('')
const highlightedIndex = ref(-1)
const triggerId = `select-${Math.random().toString(36).slice(2, 9)}`

const hasError = computed(() => !!props.error)

const selectedValues = computed(() => {
  if (props.multiple) return Array.isArray(props.modelValue) ? props.modelValue : []
  return props.modelValue != null ? [props.modelValue] : []
})

const hasSelection = computed(() => selectedValues.value.length > 0)

const selectedOptions = computed(() =>
  props.options.filter((opt) => selectedValues.value.includes(opt.value))
)

const filteredOptions = computed(() => {
  if (!searchTerm.value.trim()) return props.options
  const term = searchTerm.value.toLowerCase()
  return props.options.filter((opt) => opt.label.toLowerCase().includes(term))
})

function isSelected(value) {
  return selectedValues.value.includes(value)
}

function handleTriggerClick() {
  if (props.disabled) return
  isOpen.value ? close() : open()
}

function open() {
  isOpen.value = true
  highlightedIndex.value = filteredOptions.value.findIndex((o) => isSelected(o.value))
  emit('open')
  nextTick(() => {
    if (props.searchable) searchEl.value?.focus()
  })
}

function close() {
  isOpen.value = false
  searchTerm.value = ''
  highlightedIndex.value = -1
  emit('close')
}

function selectOption(opt) {
  if (props.multiple) {
    const current = Array.isArray(props.modelValue) ? [...props.modelValue] : []
    const idx = current.indexOf(opt.value)
    if (idx === -1) {
      current.push(opt.value)
    } else {
      current.splice(idx, 1)
    }
    emit('update:modelValue', current)
    searchTerm.value = ''
    nextTick(() => searchEl.value?.focus())
  } else {
    emit('update:modelValue', opt.value)
    close()
  }
}

function removeValue(value) {
  if (!props.multiple) return
  const current = Array.isArray(props.modelValue) ? props.modelValue.filter((v) => v !== value) : []
  emit('update:modelValue', current)
}

function clearAll() {
  emit('update:modelValue', props.multiple ? [] : null)
}

function handleSearchKeydown(e) {
  const optionsCount = filteredOptions.value.length

  if (e.key === 'ArrowDown') {
    e.preventDefault()
    highlightedIndex.value = (highlightedIndex.value + 1) % optionsCount
    scrollHighlightedIntoView()
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    highlightedIndex.value = (highlightedIndex.value - 1 + optionsCount) % optionsCount
    scrollHighlightedIntoView()
  } else if (e.key === 'Enter') {
    e.preventDefault()
    const opt = filteredOptions.value[highlightedIndex.value]
    if (opt) selectOption(opt)
  } else if (e.key === 'Escape') {
    close()
  } else if (e.key === 'Backspace' && !searchTerm.value && props.multiple && selectedValues.value.length) {
    // quick-remove last tag, Select2-style
    removeValue(selectedValues.value[selectedValues.value.length - 1])
  }
}

function scrollHighlightedIntoView() {
  nextTick(() => {
    const el = listEl.value?.children[highlightedIndex.value]
    el?.scrollIntoView({ block: 'nearest' })
  })
}

function handleClickOutside(e) {
  if (rootEl.value && !rootEl.value.contains(e.target)) {
    close()
  }
}

watch(searchTerm, () => {
  highlightedIndex.value = filteredOptions.value.length ? 0 : -1
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>