<template>
  <Fieldset legend="Website customization" description="Colors and typography used across this tenant's public website.">
    <div class="gap-6 grid grid-cols-1 lg:grid-cols-2">
      <!-- Controls -->
      <div class="flex flex-col gap-5">
        <div class="gap-4 grid grid-cols-2">
          <ColorField :model-value="form.primaryColor" label="Primary color" @update:model-value="updateField('primaryColor', $event)" />
          <ColorField :model-value="form.accentColor" label="Accent color" @update:model-value="updateField('accentColor', $event)" />
        </div>

        <div class="gap-4 grid grid-cols-2">
          <ColorField :model-value="form.backgroundColor" label="Background color" @update:model-value="updateField('backgroundColor', $event)" />
          <ColorField :model-value="form.textColor" label="Text color" @update:model-value="updateField('textColor', $event)" />
        </div>

        <Select :model-value="form.fontFamily" label="Font" :searchable="false" :clearable="false" :options="fontOptions" @update:model-value="updateField('fontFamily', $event)" />

        

        <BaseRadioGroup :model-value="form.buttonStyle" label="Button style" :options="buttonStyleOptions" @update:model-value="updateField('buttonStyle', $event)" />

        <Button size="sm" variant="ghost" class="self-start" @click="resetToDefaults">
          Reset to defaults
        </Button>
      </div>

      <!-- Live preview -->
      <div class="flex flex-col gap-2">
        <p class="font-medium text-neutral-500 dark:text-neutral-400 text-xs">Preview</p>
        <div class="shadow-sm border border-neutral-300 dark:border-neutral-700 rounded-lg overflow-hidden" :style="{
          backgroundColor: form.backgroundColor,
          color: form.textColor,
          fontFamily: previewFontStack,
        }">
          <div class="flex justify-between items-center px-5 py-4" :style="{ backgroundColor: form.primaryColor }">
            <span class="font-semibold text-white">{{ tenantName || 'Your Business' }}</span>
            <div class="flex items-center gap-4 text-white/90 text-sm">
              <span>Home</span>
              <span>Services</span>
              <span>Contact</span>
            </div>
          </div>

          <div class="flex flex-col items-start gap-3 px-5 py-8">
            <h2 class="font-semibold text-xl" :style="{ color: form.textColor }">
              Welcome to {{ tenantName || 'Your Business' }}
            </h2>
            <p class="opacity-80 max-w-sm text-sm">
              {{ tenantBio || 'A short tagline describing what this business offers goes here.' }}
            </p>
            <button type="button" class="hover:opacity-90 mt-2 px-4 py-2 font-medium text-white text-sm transition-opacity" :style="{
              backgroundColor: form.accentColor,
              borderRadius: buttonRadius,
            }">
              Get started
            </button>
          </div>
        </div>
      </div>
    </div>
  </Fieldset>
</template>

<script setup>
/**
 * TenantCustomizationSection.vue — colors, font, and button style
 * for the tenant's public-facing website, with a live preview panel
 * that updates as fields change.
 *
 * Usage:
 * <TenantCustomizationSection
 *   v-model="form.customization"
 *   :tenant-name="form.identity.tradeName || form.legal.tradeName"
 *   :tenant-bio="form.identity.bio"
 * />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import ColorField from '@/components/ColorField.vue'
import Select from '@/components/Select.vue'
import BaseRadioGroup from '@/components/BaseRadioGroup.vue'
import Button from '@/components/Button.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true, // { primaryColor, accentColor, backgroundColor, textColor, fontFamily, buttonStyle }
  },
  tenantName: { type: String, default: '' },
  tenantBio: { type: String, default: '' },
  fontOptions: {
    type: Array,
    default: () => [
      { label: 'Inter (default)', value: 'inter' },
      { label: 'Roboto', value: 'roboto' },
      { label: 'Poppins', value: 'poppins' },
      { label: 'Merriweather (serif)', value: 'merriweather' },
    ],
  },
  buttonStyleOptions: {
    type: Array,
    default: () => [
      { label: 'Rounded', value: 'rounded' },
      { label: 'Pill', value: 'pill' },
      { label: 'Square', value: 'square' },
    ],
  },
  defaults: {
    type: Object,
    default: () => ({
      primaryColor: '#10B981',
      accentColor: '#D4AF37',
      backgroundColor: '#FFFFFF',
      textColor: '#171717',
      fontFamily: 'inter',
      buttonStyle: 'rounded',
    }),
  },
})

const emit = defineEmits(['update:modelValue'])

function updateField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

function resetToDefaults() {
  emit('update:modelValue', { ...props.defaults })
}

const form = computed(() => props.modelValue)

const fontStackMap = {
  inter: "'Inter', system-ui, sans-serif",
  roboto: "'Roboto', system-ui, sans-serif",
  poppins: "'Poppins', system-ui, sans-serif",
  merriweather: "'Merriweather', Georgia, serif",
}

const previewFontStack = computed(
  () => fontStackMap[form.value.fontFamily] ?? fontStackMap.inter
)

const buttonRadius = computed(() => {
  const map = { rounded: '0.375rem', pill: '9999px', square: '0px' }
  return map[form.value.buttonStyle] ?? map.rounded
})
</script>
