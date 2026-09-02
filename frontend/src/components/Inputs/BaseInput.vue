<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" :for="inputId" class="text-sm font-medium dark:text-neutral-50 text-neutral-900">
      {{ label }}
      <span v-if="required" class="text-emerald-500">*</span>
    </label>

    <div class="relative">
      <span v-if="$slots.prefix" class="absolute left-3 top-1/2 -translate-y-1/2 dark:text-neutral-400 text-neutral-500">
        <slot name="prefix" />
      </span>

      <input
        :id="inputId"
        v-model="value"
        :type="resolvedType"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        :class="[
          'w-full rounded-md py-2 text-sm transition-colors',
          $slots.prefix ? 'pl-9' : 'pl-3',
          type === 'password' ? 'pr-9' : 'pr-3',
          'dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900',
          'placeholder:text-neutral-500 dark:placeholder:text-neutral-400',
          'border focus:outline-none focus:ring-1',
          hasError
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
            : 'border-transparent focus:border-emerald-500 focus:ring-emerald-500',
          disabled ? 'opacity-50 cursor-not-allowed' : '',
        ]"
        v-bind="$attrs"
      />

      <button
        v-if="type === 'password'"
        type="button"
        class="absolute right-2.5 top-1/2 -translate-y-1/2 dark:text-neutral-400 text-neutral-500 hover:text-emerald-500"
        :aria-label="showPassword ? 'Hide password' : 'Show password'"
        @click="showPassword = !showPassword"
      >
        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
          <line x1="1" y1="1" x2="23" y2="23" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
          <circle cx="12" cy="12" r="3" />
        </svg>
      </button>
    </div>

    <p v-if="hasError" class="text-xs text-red-500">{{ error }}</p>
    <p v-else-if="hint" class="text-xs dark:text-neutral-400 text-neutral-500">{{ hint }}</p>
  </div>
</template>

<script setup>
/**
 * BaseInput.vue — text / password / date / number input.
 * Dynamic binding via v-model; all native attrs (name, autocomplete,
 * pattern, etc.) pass through via v-bind="$attrs".
 *
 * Usage:
 * <BaseInput v-model="form.email" type="text" label="Email" placeholder="you@example.com" />
 * <BaseInput v-model="form.password" type="password" label="Password" />
 * <BaseInput v-model="form.birthday" type="date" label="Birthday" />
 * <BaseInput v-model="form.age" type="number" label="Age" :min="0" />
 */
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  type: {
    type: String,
    default: 'text', // 'text' | 'password' | 'date' | 'number' | any native input type
  },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  hint: { type: String, default: '' },
  error: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  min: { type: [String, Number], default: undefined },
  max: { type: [String, Number], default: undefined },
  step: { type: [String, Number], default: undefined },
})

const emit = defineEmits(['update:modelValue'])

defineOptions({ inheritAttrs: false })

const inputId = `input-${Math.random().toString(36).slice(2, 9)}`
const showPassword = ref(false)

const value = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})

const resolvedType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password'
  }
  return props.type
})

const hasError = computed(() => !!props.error)
</script>
