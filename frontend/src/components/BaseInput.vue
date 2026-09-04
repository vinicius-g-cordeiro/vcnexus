<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" :for="inputId" class="font-medium text-zinc-900 dark:text-zinc-50 text-sm">
      {{ label }}
      <span v-if="required" class="text-emerald-500">*</span>
    </label>

    <div class="relative">
      <span v-if="$slots.prefix" class="top-1/2 left-3 absolute text-zinc-500 dark:text-zinc-400 -translate-y-1/2">
        <slot name="prefix" />
      </span>

      <input :id="inputId" v-model="value" :type="resolvedType" :placeholder="placeholder" :disabled="disabled" :required="required" :min="min" :max="max" :step="step" :class="[
        'w-full rounded-md py-2 text-sm transition-colors',
        $slots.prefix ? 'pl-9' : 'pl-3',
        type === 'password' ? 'pr-9' : 'pr-3',
        'dark:bg-zinc-800 bg-neutral-100 dark:text-zinc-50 text-zinc-900',
        'placeholder:text-zinc-500 dark:placeholder:text-zinc-400',
        'border border-zinc-500/90 focus:outline-none focus:ring-1',
        hasError
          ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
          : 'border-zinc-100/90 focus:border-emerald-500 focus:ring-emerald-500',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
      ]" :autocomplete="autocomplete" v-bind="$attrs" />

      <button v-if="type === 'password'" type="button" class="top-1/2 right-2.5 absolute text-zinc-500 hover:text-emerald-500 dark:text-zinc-400 -translate-y-1/2" :aria-label="showPassword ? 'Hide password' : 'Show password'" @click="showPassword = !showPassword">
        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
          <line x1="1" y1="1" x2="23" y2="23" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
          <circle cx="12" cy="12" r="3" />
        </svg>
      </button>
    </div>

    <p v-if="hasError" class="text-red-500 text-xs">{{ error }}</p>
    <p v-else-if="hint" class="text-zinc-500 dark:text-zinc-400 text-xs">{{ hint }}</p>
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
  autocomplete: { type: String, default: 'off'},
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
