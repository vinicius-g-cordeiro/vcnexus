<script setup="js">
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    placeholder: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    name: { type: String, default: '' },
    id: { type: String, default: '' },
    classes: { type: String, default: '' },
    options: { type: Array, required: true }
})

const emit = defineEmits([
    'update:modelValue',
    'change'
])

const container = ref(null)
const input = ref(null)

const isOpen = ref(false)
const search = ref('')
const highlightedIndex = ref(-1)

const selectedOption = computed(() => {
    return props.options.find(
        option => String(option.value) === String(props.modelValue)
    ) || null
})

const filteredOptions = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) {
        return props.options
    }

    return props.options.filter(option =>
        String(option.label)
            .toLowerCase()
            .includes(query)
    )
})

const displayValue = computed(() => {
    if (isOpen.value) {
        return search.value
    }

    return selectedOption.value?.label || ''
})

const open = async () => {
    if (props.disabled) {
        return
    }

    isOpen.value = true
    search.value = selectedOption.value?.label || ''
    highlightedIndex.value = -1

    await nextTick()

    input.value?.focus()
}

const close = () => {
    isOpen.value = false
    search.value = ''
    highlightedIndex.value = -1
}

const toggle = () => {
    if (isOpen.value) {
        close()
    } else {
        open()
    }
}

const selectOption = (option) => {
    emit('update:modelValue', option.value)

    emit('change', {
        target: {
            value: option.value
        },
        option
    })

    close()
}

const clear = () => {
    emit('update:modelValue', '')

    emit('change', {
        target: {
            value: ''
        },
        option: null
    })

    search.value = ''
    highlightedIndex.value = -1

    if (isOpen.value) {
        nextTick(() => input.value?.focus())
    }
}

const handleInput = (event) => {
    search.value = event.target.value
    highlightedIndex.value = filteredOptions.value.length
        ? 0
        : -1

    if (!isOpen.value) {
        isOpen.value = true
    }
}

const handleKeydown = (event) => {
    if (props.disabled) {
        return
    }

    if (!isOpen.value) {
        if (
            event.key === 'ArrowDown' ||
            event.key === 'Enter' ||
            event.key === ' '
        ) {
            event.preventDefault()
            open()
        }

        return
    }

    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault()

            if (!filteredOptions.value.length) {
                return
            }

            highlightedIndex.value =
                highlightedIndex.value < filteredOptions.value.length - 1
                    ? highlightedIndex.value + 1
                    : 0

            break

        case 'ArrowUp':
            event.preventDefault()

            if (!filteredOptions.value.length) {
                return
            }

            highlightedIndex.value =
                highlightedIndex.value > 0
                    ? highlightedIndex.value - 1
                    : filteredOptions.value.length - 1

            break

        case 'Enter':
            event.preventDefault()

            if (
                highlightedIndex.value >= 0 &&
                filteredOptions.value[highlightedIndex.value]
            ) {
                selectOption(
                    filteredOptions.value[highlightedIndex.value]
                )
            }

            break

        case 'Escape':
            event.preventDefault()
            close()
            break

        case 'Tab':
            close()
            break
    }
}

const handleClickOutside = (event) => {
    if (!container.value?.contains(event.target)) {
        close()
    }
}

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<template>
    <div ref="container" class="flex flex-col gap-1 m-2">
        <label v-if="label || $slots.label" :for="id" class="flex flex-row justify-start items-center gap-1 font-semibold text-zinc-900 dark:text-zinc-50 text-xs align-baseline whitespace-nowrap" :title="label">
            <template v-if="label">
                {{ label }}:

                <span v-if="required" class="font-bold text-[0.3rem] text-tomato-500">
                    <i class="bi bi-asterisk"></i>
                </span>
            </template>

            <slot v-else name="label" />
        </label>

        <div class="relative">
            <!-- Combobox -->
            <div class="flex items-center bg-zinc-100 dark:bg-zinc-800 border border-olive-wood-500 dark:border-zinc-600 rounded-md focus-within:outline focus-within:outline-olive-wood-500" :class="[
                classes,
                {
                    'opacity-50 cursor-not-allowed': disabled
                }
            ]">
                <!-- Search icon -->
                <div class="pl-3 text-zinc-500 dark:text-zinc-400">
                    <i class="bi bi-search"></i>
                </div>

                <!-- Input -->
                <input ref="input" :id="id" :name="name" :value="displayValue" :placeholder="placeholder" :disabled="disabled" :required="required" :autofocus="autofocus" autocomplete="off" role="combobox" :aria-expanded="isOpen" aria-autocomplete="list"
                    class="bg-transparent px-2 focus:outline-none w-full text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-500 text-xs" @focus="open" @input="handleInput" @keydown="handleKeydown" />

                <!-- Clear -->
                <button v-if="modelValue !== '' && !disabled" type="button" class="px-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200" tabindex="-1" @mousedown.prevent @click="clear">
                    <i class="bi bi-x"></i>
                </button>

                <!-- Dropdown button -->
                <button type="button" class="px-3 border-zinc-300 dark:border-zinc-600 border-l text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200" :disabled="disabled" tabindex="-1" @mousedown.prevent @click="toggle">
                    <i class="bi" :class="isOpen
                        ? 'bi-chevron-up'
                        : 'bi-chevron-down'"></i>
                </button>
            </div>

            <!-- Dropdown -->
            <div v-if="isOpen" class="right-0 left-0 z-50 absolute bg-zinc-100 dark:bg-zinc-800 shadow-lg mt-1 border border-zinc-300 dark:border-zinc-600 rounded-md max-h-60 overflow-y-auto">
                <!-- No results -->
                <div v-if="!filteredOptions.length" class="px-3 py-2 text-zinc-500 dark:text-zinc-400 text-xs">
                    No results found.
                </div>

                <!-- Options -->
                <button v-for="(option, index) in filteredOptions" :key="option.value" type="button" class="flex items-center px-3 py-2 w-full text-xs text-left transition-colors" :class="[
                    index === highlightedIndex
                        ? 'bg-zinc-200 dark:bg-zinc-700'
                        : 'hover:bg-zinc-200 dark:hover:bg-zinc-700',

                    String(option.value) === String(modelValue)
                        ? 'font-semibold'
                        : ''
                ]" @mousedown.prevent @click="selectOption(option)" @mouseenter="highlightedIndex = index">
                    <span class="flex-1">
                        {{ option.label }}
                    </span>

                    <i v-if="String(option.value) === String(modelValue)" class="ml-2 bi bi-check"></i>
                </button>
            </div>
        </div>

        <p v-if="error" class="text-olive-wood-500 text-xs">
            {{ error }}
        </p>
    </div>
</template>
