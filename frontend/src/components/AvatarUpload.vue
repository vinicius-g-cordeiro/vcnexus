<template>
  <Fieldset legend="Avatar" description="This is displayed on your profile and across the system.">
    <div class="flex items-center gap-6">
      <div class="relative shrink-0">
        <img
          v-if="previewUrl"
          :src="previewUrl"
          :alt="name"
          class="rounded-full ring-2 ring-emerald-500 w-20 h-20 object-cover"
        />
        <span
          v-else
          class="flex justify-center items-center bg-emerald-500 rounded-full ring-2 ring-emerald-500 w-20 h-20 font-semibold text-white text-xl"
        >
          {{ initials }}
        </span>

        <button
          v-if="previewUrl"
          type="button"
          class="-top-1 -right-1 absolute bg-red-500 hover:bg-red-600 p-1 rounded-full text-white transition-colors"
          aria-label="Remove avatar"
          @click="removeAvatar"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>

      <div class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
          <AppButton size="sm" variant="outline" @click="triggerFilePicker">
            {{ previewUrl ? 'Change photo' : 'Upload photo' }}
          </AppButton>
          <AppButton v-if="previewUrl" size="sm" variant="ghost" @click="removeAvatar">
            Remove
          </AppButton>
        </div>
        <p class="text-neutral-500 dark:text-neutral-400 text-xs">
          JPG, PNG or GIF. Max {{ maxSizeMb }}MB.
        </p>
        <p v-if="error" class="text-red-500 text-xs">{{ error }}</p>
      </div>

      <input
        ref="fileInput"
        type="file"
        accept="image/png, image/jpeg, image/gif"
        class="hidden"
        @change="handleFileChange"
      />
    </div>
  </Fieldset>
</template>

<script setup>
/**
 * AvatarUpload.vue — avatar preview + file picker for the profile
 * settings page. Emits the raw File object on `update:file` so the
 * parent can handle the actual upload request; `modelValue` is the
 * currently-saved avatar URL (shown until a new file is picked).
 *
 * Usage:
 * <AvatarUpload v-model="user.avatarUrl" :name="user.fullName" @update:file="onAvatarFile" />
 */
import { ref, computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import AppButton from '@/components/AppButton.vue'

const props = defineProps({
  modelValue: { type: String, default: '' }, // saved avatar URL
  name: { type: String, default: '' },        // for initials fallback
  maxSizeMb: { type: Number, default: 5 },
})

const emit = defineEmits(['update:modelValue', 'update:file'])

const fileInput = ref(null)
const localPreview = ref('')
const error = ref('')

const previewUrl = computed(() => localPreview.value || props.modelValue)

const initials = computed(() =>
  (props.name || '?')
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)

function triggerFilePicker() {
  fileInput.value?.click()
}

function handleFileChange(e) {
  const file = e.target.files?.[0]
  if (!file) return

  error.value = ''

  if (file.size > props.maxSizeMb * 1024 * 1024) {
    error.value = `File is too large. Max ${props.maxSizeMb}MB.`
    e.target.value = ''
    return
  }

  localPreview.value = URL.createObjectURL(file)
  emit('update:file', file)
}

function removeAvatar() {
  localPreview.value = ''
  error.value = ''
  if (fileInput.value) fileInput.value.value = ''
  emit('update:modelValue', '')
  emit('update:file', null)
}
</script>
