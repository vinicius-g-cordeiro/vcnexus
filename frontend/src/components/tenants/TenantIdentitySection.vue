<template>
  <Fieldset legend="Identity" description="How this tenant is addressed and described across the system.">
    <div class="gap-5 grid grid-cols-1 sm:grid-cols-2">
      <BaseInput :model-value="form.domain" label="Custom domain" placeholder="app.vcnexus.com" hint="Optional. Leave blank to use the default subdomain." :error="errors.domain" @update:model-value="updateField('domain', $event)" />

      <div class="flex flex-col gap-1.5">
        <label :for="slugId" class="font-medium text-neutral-900 dark:text-neutral-50 text-sm">
          Slug
        </label>
        <div :class="[
          'flex items-stretch rounded-md text-sm transition-colors overflow-hidden',
          'border focus-within:ring-1',
          errors.slug
            ? 'border-red-500 focus-within:border-red-500 focus-within:ring-red-500'
            : 'border-transparent focus-within:border-emerald-500 focus-within:ring-emerald-500',
        ]">
          <span class="flex items-center bg-neutral-300 dark:bg-neutral-700 px-3 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
            {{ baseUrl }}/
          </span>
          <input :id="slugId" :value="form.slug" type="text" placeholder="vcNexus" class="bg-neutral-200 dark:bg-neutral-800 px-3 py-2 border-none focus:outline-none focus:ring-0 w-full min-w-0 text-neutral-900 dark:placeholder:text-neutral-400 dark:text-neutral-50 placeholder:text-neutral-500"
            @input="handleSlugInput" />
        </div>
        <p v-if="errors.slug" class="text-red-500 text-xs">{{ errors.slug }}</p>
        <p v-else class="text-neutral-500 dark:text-neutral-400 text-xs">
          Lowercase letters, numbers and hyphens only.
        </p>
      </div>
    </div>

    <div class="flex flex-col gap-1.5">
      <label :for="descriptionId" class="font-medium text-neutral-900 dark:text-neutral-50 text-sm">
        Description
      </label>
      <textarea :id="descriptionId" :value="form.description" rows="4" placeholder="A longer description of what this tenant offers..." :class="[
        'w-full rounded-md px-3 py-2 text-sm transition-colors resize-y',
        'dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900',
        'placeholder:text-neutral-500 dark:placeholder:text-neutral-400',
        'border focus:outline-none focus:ring-1',
        errors.description
          ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
          : 'border-transparent focus:border-emerald-500 focus:ring-emerald-500',
      ]" @input="updateField('description', $event.target.value)" />
      <p v-if="errors.description" class="text-red-500 text-xs">{{ errors.description }}</p>
    </div>
  </Fieldset>
</template>

<script setup>
/**
 * TenantIdentitySection.vue — domain, slug (with auto-slugify),
 * short bio, and a longer description.
 *
 * The slug input auto-lowercases and strips invalid characters as
 * the user types, mirroring how most platforms handle slugs.
 *
 * Usage:
 * <TenantIdentitySection v-model="form.identity" :errors="errors.identity" />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BioSection from '@/components/BioSection.vue'
import BaseInput from '@/components/BaseInput.vue'
import Select from '@/components/Select.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { domain, slug, bio, description }
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  baseUrl: {
    type: String,
    default: 'app.vcnexus.com',
  },
})

const emit = defineEmits(['update:modelValue'])

const slugId = `slug-${Math.random().toString(36).slice(2, 9)}`
const descriptionId = `description-${Math.random().toString(36).slice(2, 9)}`

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

function handleSlugInput(e) {
  const slugified = e.target.value
    .toLowerCase()
    .replace(/[^a-z0-9-]+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '')
  updateField('slug', slugified)
}

const form = computed(() => props.modelValue)
</script>
