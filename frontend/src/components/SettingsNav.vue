<template>
  <nav class="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible pb-2 lg:pb-0">
    <button
      v-for="section in sections"
      :key="section.key"
      type="button"
      :class="[
        'flex items-center gap-2.5 whitespace-nowrap rounded-md px-3 py-2 text-sm font-medium transition-colors text-left shrink-0',
        modelValue === section.key
          ? 'bg-emerald-500 text-white'
          : 'dark:text-neutral-300 text-neutral-700 hover:bg-neutral-300 dark:hover:bg-neutral-700',
      ]"
      @click="$emit('update:modelValue', section.key)"
    >
      <span v-if="section.icon" class="h-4 w-4 shrink-0 [&>svg]:h-4 [&>svg]:w-4" v-html="section.icon" />
      {{ section.label }}
    </button>
  </nav>
</template>

<script setup>
/**
 * SettingsNav.vue — vertical tab list for a settings page. Scrolls
 * horizontally on mobile instead of wrapping.
 *
 * Usage:
 * <SettingsNav v-model="activeSection" :sections="[{ key: 'avatar', label: 'Avatar' }]" />
 */
defineProps({
  modelValue: { type: String, required: true },
  sections: {
    type: Array,
    required: true, // [{ key, label, icon? (raw svg string) }]
  },
})

defineEmits(['update:modelValue'])
</script>
