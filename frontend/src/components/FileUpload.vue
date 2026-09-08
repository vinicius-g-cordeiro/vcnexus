<template>
  <div class="flex flex-col gap-4 my-4">
    <label
      v-if="label"
      for="files"
      class="block my-4 font-medium text-neutral-900 dark:text-neutral-100 text-sm text-start"
    >
      {{ label }}
    </label>

    <!-- Toolbar (only when multiple files are allowed) -->
    <div v-if="limit > 1" class="flex items-center gap-3">
      <button
        type="button"
        @click="addFile"
        :disabled="files.length >= limit"
        title="Add file"
        class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary/80 disabled:opacity-50 px-3 py-1.5 rounded-md font-medium text-neutral-100 text-sm transition-colors disabled:cursor-not-allowed"
      >
        <PlusIcon v-bind="iconProps" />
        <span>Add</span>
      </button>
      <button
        type="button"
        @click="clearFiles"
        :disabled="files.length === 0"
        title="Remove all files"
        class="inline-flex items-center gap-1.5 hover:bg-red-50 dark:hover:bg-red-950/40 disabled:opacity-50 px-3 py-1.5 border border-red-200 dark:border-red-900 rounded-md font-medium text-red-600 dark:text-red-400 text-sm transition-colors disabled:cursor-not-allowed"
      >
        <ClearAllIcon v-bind="iconProps" />
        <span>Clear all</span>
      </button>
      <span class="ml-auto text-neutral-500 dark:text-neutral-400 text-xs">{{ files.length }} / {{ limit }}</span>
    </div>

    <!-- Single file mode -->
    <div v-if="limit <= 1" class="flex flex-col gap-4">
      <div v-for="(file, index) in files" :key="index" class="flex flex-col gap-4">
        <select
          v-if="selectType"
          v-model="file.type"
          :name="`${name}_type`"
          :class="selectClass"
        >
          <option v-for="opt in optionsType" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>

        <input
          type="file"
          :id="`file${index}`"
          :ref="(el) => setInputRef(el, index)"
          :name="`${name}0`"
          :accept="accept"
          class="bg-neutral-100 dark:bg-neutral-800 px-2 py-1 border border-primary focus:border-primary rounded focus:ring-0 text-neutral-800 dark:text-neutral-200 accent-primary focus:accent-primary form-control"
          :class="inputClass"
          :required="required"
          :disabled="disabled"
          :autofocus="autofocus"
          :autocomplete="autocomplete"
          @change="(e) => updatePreview(e, index)"
        />

        <div v-if="file.pdf || file.url || file.video" class="relative flex justify-center items-center w-64 h-64">
          <embed v-if="file.pdf" :src="file.pdf" type="application/pdf" width="256" height="256" class="w-64 h-64" />
          <img v-else-if="file.url" :src="file.url" class="rounded-md w-64 h-64 object-cover" />
          <video v-else-if="file.video" :src="file.video" class="rounded-md w-64 h-64 object-cover" controls></video>

          <!-- Remove this file (does not touch other slots) -->
          <button
            type="button"
            @click="removeFile(index)"
            title="Remove file"
            class="inline-flex top-1.5 right-1.5 absolute justify-center items-center bg-neutral-900/70 hover:bg-red-600 p-1 rounded-full text-white transition-colors"
          >
            <RemoveIcon v-bind="iconProps" />
          </button>
        </div>

        <div v-if="file.progress !== null" class="bg-neutral-200 dark:bg-neutral-700 rounded-full w-full h-2 overflow-hidden">
          <div
            class="bg-primary h-2 transition-all duration-300"
            :style="{ width: `${file.progress}%` }"
          ></div>
        </div>

        <div v-if="file.url || file.pdf || file.video" class="flex flex-row gap-4 mt-auto mb-2">
          <a :href="file.url" target="_blank" title="Open in new tab" class="inline-flex items-center gap-1 mt-2 text-primary hover:text-primary/80 text-sm transition-colors">
            <ExternalLinkIcon v-bind="iconProps" />
          </a>
          <a :href="file.url" download title="Download" class="inline-flex items-center gap-1 mt-2 text-green-600 hover:text-green-700 dark:text-green-500 text-sm transition-colors">
            <DownloadIcon v-bind="iconProps" />
          </a>
        </div>
      </div>
    </div>

    <!-- Multi file mode -->
    <div v-if="limit > 1" class="gap-4 grid grid-cols-3 md:grid-cols-3">
      <div
        v-for="(file, index) in files"
        :key="index"
        class="flex flex-col gap-4 shadow-[0_0_10px_0_rgba(0,0,0,0.1)] p-4 rounded-md"
      >
        <select
          v-if="selectType"
          v-model="file.type"
          :name="`${name}_type`"
          :class="selectClass"
        >
          <option v-for="opt in optionsType" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>

        <input
          type="file"
          :id="`file${index}`"
          :ref="(el) => setInputRef(el, index)"
          :name="`${name}${index}`"
          :accept="accept"
          class="bg-neutral-100 dark:bg-neutral-800 px-2 py-1 border border-primary focus:border-primary rounded focus:ring-0 text-neutral-800 dark:text-neutral-200 accent-primary focus:accent-primary form-control"
          :class="inputClass"
          :required="required"
          :disabled="disabled"
          :autofocus="autofocus"
          :autocomplete="autocomplete"
          @change="(e) => updatePreview(e, index)"
        />

        <div v-if="file.pdf || file.url || file.video" class="relative flex justify-center items-center w-full h-40">
          <embed v-if="file.pdf" :src="file.pdf" type="application/pdf" class="w-full h-40" />
          <img v-else-if="file.url" :src="file.url" class="rounded-md w-full h-40 object-contain" />
          <video v-else-if="file.video" :src="file.video" class="rounded-md w-full h-40 object-contain" controls></video>

          <!-- Remove only this file/card, not the whole list -->
          <button
            type="button"
            @click="removeInputFile(index)"
            title="Remove this file"
            class="inline-flex top-1.5 right-1.5 absolute justify-center items-center bg-neutral-900/70 hover:bg-red-600 p-1 rounded-full text-white transition-colors"
          >
            <RemoveIcon v-bind="iconProps" />
          </button>
        </div>

        <div v-if="file.progress !== null" class="bg-neutral-200 dark:bg-neutral-700 rounded-full w-full h-2 overflow-hidden">
          <div
            class="bg-primary h-2 transition-all duration-300"
            :style="{ width: `${file.progress}%` }"
          ></div>
        </div>

        <div v-if="file.url || file.pdf || file.video" class="flex flex-row gap-4 mt-auto mb-2">
          <a :href="file.url" target="_blank" title="Open in new tab" class="inline-flex items-center gap-1 mt-2 text-primary hover:text-primary/80 text-sm transition-colors">
            <ExternalLinkIcon v-bind="iconProps" />
          </a>
          <a :href="file.url" download title="Download" class="inline-flex items-center gap-1 mt-2 text-green-600 hover:text-green-700 dark:text-green-500 text-sm transition-colors">
            <DownloadIcon v-bind="iconProps" />
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue'
import {
  Plus as PlusIcon,
  Trash2 as ClearAllIcon,
  X as RemoveIcon,
  ExternalLink as ExternalLinkIcon,
  Download as DownloadIcon,
} from '@lucide/vue'

const props = defineProps({
  // Field identity / behavior
  name: { type: String, default: 'files' },
  label: { type: String, default: null },
  limit: { type: Number, default: 1 },
  accept: { type: String, default: '.jpg,.jpeg,.png,.webp,application/pdf,image/*,video/*,audio/*' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  autofocus: { type: Boolean, default: false },
  autocomplete: { type: String, default: null },
  inputClass: { type: [String, Array, Object], default: '' },

  // Optional "type" selector per file, mirrors options.selectType
  selectType: { type: Boolean, default: false },
  optionsType: { type: Array, default: () => [] }, // [{ value, label }]
  selectedType: { type: [String, Number], default: null },
  selectClass: { type: [String, Array, Object], default: '' },

  // Icon sizing — number is treated as pixels (matches Lucide's `:size` prop)
  iconSize: { type: [Number, String], default: 16 },
  iconStrokeWidth: { type: [Number, String], default: 2 },

  // Pre-populate a single existing file, mirrors the Alpine init({name, url})
  modelValue: {
    type: [Object, Array],
    default: null,
    // Shape for limit<=1: { name: string, url: string }
    // Shape for limit>1: [{ name: string, url: string }, ...]
  },
})

const emit = defineEmits(['update:modelValue', 'change', 'upload-start', 'upload-complete'])

/**
 * files[i] shape:
 * {
 *   file: File | null,   // the underlying native File object (for actual upload)
 *   url: string | null,  // image preview / generic data URL
 *   pdf: string | null,  // object URL for pdf preview
 *   video: string | null,// object URL for video preview
 *   progress: number | null,
 *   type: string | null, // only used when selectType is true
 * }
 */
const files = reactive([])
const inputRefs = ref([])

// Shared bind object so every icon respects the same size/stroke props
const iconProps = computed(() => ({
  size: Number(props.iconSize),
  strokeWidth: Number(props.iconStrokeWidth),
}))

function setInputRef(el, index) {
  if (el) inputRefs.value[index] = el
}

function blankFile() {
  return { file: null, url: null, pdf: null, video: null, progress: null, type: props.selectedType }
}

function addFile() {
  if (files.length >= props.limit) return
  files.push(blankFile())
}

function clearFiles() {
  files.splice(0, files.length)
  inputRefs.value = []
}

function removeFile(index) {
  // single-file mode: reset in place, same as the original removeFile()
  files[index].url = null
  files[index].pdf = null
  files[index].video = null
  files[index].progress = null
  files[index].file = null
  const inputEl = inputRefs.value[index]
  if (inputEl) inputEl.value = null
  emitModel()
}

function removeInputFile(index) {
  // multi-file mode: drop the entry entirely, same as removeInputFile()
  files.splice(index, 1)
  inputRefs.value.splice(index, 1)
  emitModel()
}

function updatePreview(event, index) {
  const target = event.target
  const file = target.files && target.files[0]

  files[index].url = null
  files[index].pdf = null
  files[index].video = null
  files[index].progress = null
  files[index].file = file || null

  if (!file) return

  // PDF preview
  if (file.type === 'application/pdf') {
    files[index].pdf = URL.createObjectURL(file)
    emit('change', { index, file })
    emitModel()
    return
  }

  // Video preview
  if (['video/mp4', 'video/ogg', 'video/webm'].includes(file.type)) {
    files[index].video = URL.createObjectURL(file)
    emit('change', { index, file })
    emitModel()
    return
  }

  // Image / generic preview via FileReader, with simulated progress
  // (mirrors the original component, which never wired real xhr progress events either)
  files[index].progress = 0
  emit('upload-start', { index, file })

  const reader = new FileReader()

  reader.onprogress = (e) => {
    if (e.lengthComputable) {
      files[index].progress = Math.round((e.loaded / e.total) * 100)
    }
  }

  reader.onload = (e) => {
    files[index].url = e.target.result
  }

  reader.onloadend = (e) => {
    files[index].url = e.target.result
    files[index].progress = 100
    emit('upload-complete', { index, file, dataUrl: e.target.result })
    emit('change', { index, file })
    emitModel()
  }

  reader.readAsDataURL(file)
}

function emitModel() {
  const payload = files.map((f) => ({
    file: f.file,
    url: f.url,
    type: f.type,
  }))
  emit('update:modelValue', props.limit <= 1 ? (payload[0] || null) : payload)
}

/**
 * Load an existing remote file into the given slot, converting it to a File
 * object and firing the same preview logic as a real <input> change.
 * Mirrors the Alpine init(inFile) behavior.
 */
async function loadExistingFile(index, { name, url }) {
  if (!url) {
    if (!files[index]) files[index] = blankFile()
    return
  }

  const response = await fetch(url)
  const blob = await response.blob()
  const file = new File([blob], name || 'file', { type: blob.type, lastModified: Date.now() })

  const dt = new DataTransfer()
  dt.items.add(file)

  await import('vue').then(() => {}) // no-op, keeps bundlers happy with dynamic reactive timing
  requestAnimationFrame(() => {
    const inputEl = inputRefs.value[index]
    if (inputEl) {
      inputEl.files = dt.files
      inputEl.dispatchEvent(new Event('change'))
    }
  })
}

onMounted(() => {
  if (!props.modelValue) {
    // start with one empty slot so the input renders
    addFile()
    return
  }

  if (props.limit <= 1) {
    addFile()
    loadExistingFile(0, props.modelValue)
  } else {
    const initial = Array.isArray(props.modelValue) ? props.modelValue : [props.modelValue]
    initial.forEach((entry, i) => {
      addFile()
      loadExistingFile(i, entry)
    })
  }
})

defineExpose({
  files,
  addFile,
  clearFiles,
  removeFile,
  removeInputFile,
})
</script>