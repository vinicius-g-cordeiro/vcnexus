<template>
  <Fieldset legend="Bio" description="A short description shown on your public profile.">
    <div class="flex flex-col gap-1.5">
      <label :for="textareaId" class="text-sm font-medium dark:text-neutral-50 text-neutral-900">
        About you
      </label>
      <textarea
        :id="textareaId"
        :value="modelValue"
        :maxlength="maxLength"
        rows="5"
        placeholder="Tell us a bit about yourself..."
        :class="[
          'w-full rounded-md px-3 py-2 text-sm transition-colors resize-y',
          'dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900',
          'placeholder:text-neutral-500 dark:placeholder:text-neutral-400',
          'border focus:outline-none focus:ring-1',
          error
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
            : 'border-transparent focus:border-emerald-500 focus:ring-emerald-500',
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
      />
      <div class="flex items-center justify-between">
        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
        <span v-else />
        <span class="text-xs dark:text-neutral-400 text-neutral-500">
          {{ modelValue?.length ?? 0 }}/{{ maxLength }}
        </span>
      </div>
    </div>
  </Fieldset>
</template>

<script setup>
/**
 * BioSection.vue — plain textarea for a short profile bio, with a
 * live character counter.
 *
 * Usage:
 * <BioSection v-model="form.bio" :error="errors.bio" />
 */
import Fieldset from './Fieldset.vue'

defineProps({
  modelValue: { type: String, default: '' },
  error: { type: String, default: '' },
  maxLength: { type: Number, default: 280 },
})

defineEmits(['update:modelValue'])

const textareaId = `bio-${Math.random().toString(36).slice(2, 9)}`
</script>
