<template>
    <div
        class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-2xl">
        <div>
            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">
                Files & Documents
            </h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">
                Upload files and assign a document type to each one
            </p>
        </div>

        <div class="flex flex-col justify-center items-center gap-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-800 p-8 border-2 border-zinc-300 hover:border-zinc-400 dark:border-zinc-700 dark:hover:border-zinc-600 border-dashed rounded-lg text-center transition-colors cursor-pointer"
            @click="triggerFilePicker" @dragover.prevent @drop.prevent="handleDrop">
            <div class="flex justify-center items-center bg-zinc-200 dark:bg-zinc-700 rounded-full w-12 h-12">
                <i class="text-zinc-600 dark:text-zinc-300 text-xl bi bi-cloud-arrow-up"></i>
            </div>

            <div class="flex flex-col gap-1">
                <p class="font-medium text-zinc-800 dark:text-zinc-200 text-sm">
                    Click to upload or drag and drop
                </p>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs">
                    Images, PDF, DOC, DOCX, XLS, XLSX, CSV, TXT
                </p>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs">
                    {{ files.length }} / {{ maxFiles }} files · Up to {{ maxFileSizeLabel }} per file
                </p>
            </div>

            <input ref="fileInput" type="file" multiple :accept="acceptedExtensions" class="hidden"
                @change="handleFileChange" />
        </div>

        <div v-if="errorMessage" class="flex items-center gap-2 text-red-500 text-sm">
            <i class="bi bi-exclamation-circle"></i>
            <span>{{ errorMessage }}</span>
        </div>

        <div v-if="files.length" class="flex justify-between items-center">
            <h3 class="font-medium text-zinc-800 dark:text-zinc-200 text-sm">
                Selected files ({{ files.length }})
            </h3>

            <button type="button" @click="removeAll" class="text-red-500 hover:text-red-600 text-xs transition-colors">
                Remove all
            </button>
        </div>

        <div v-if="files.length" class="flex flex-col gap-4">
            <div v-for="item in files" :key="item.id"
                class="flex flex-col gap-3 p-3 border border-zinc-200 dark:border-zinc-800 rounded-lg">
                <!-- Preview -->
                <div
                    class="relative flex justify-center items-center bg-zinc-100 dark:bg-zinc-800 rounded-md min-h-32 max-h-64 overflow-hidden">
                    <img v-if="item.kind === 'image'" :src="item.preview" :alt="item.file.name"
                        class="max-w-full max-h-64 object-contain" />

                    <iframe v-else-if="item.kind === 'pdf'" :src="item.preview" :title="item.file.name"
                        class="bg-white w-full h-64"></iframe>

                    <div v-else class="flex flex-col justify-center items-center gap-2 p-6 text-center">
                        <i :class="iconFor(item.kind)" class="text-zinc-500 dark:text-zinc-400 text-4xl"></i>
                        <p class="font-medium text-zinc-700 dark:text-zinc-200 text-sm break-all">
                            {{ item.file.name }}
                        </p>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs">
                            {{ extensionOf(item.file.name).toUpperCase() }} · {{ formatFileSize(item.file.size) }}
                        </p>
                        <a :href="item.preview" :download="item.file.name" target="_blank" rel="noopener noreferrer"
                            @click.stop
                            class="text-zinc-700 hover:text-zinc-950 dark:hover:text-white dark:text-zinc-300 text-xs underline">
                            Open or download file
                        </a>
                    </div>

                    <button type="button" @click="removeFile(item.id)" :aria-label="`Remove ${item.file.name}`"
                        class="top-2 right-2 absolute flex justify-center items-center bg-black/60 hover:bg-red-600 rounded-full w-8 h-8 text-white transition-colors">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>

                <!-- File information -->
                <div class="flex items-start gap-3">
                    <i :class="iconFor(item.kind)" class="mt-1 text-zinc-500 dark:text-zinc-400 text-xl"></i>

                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-zinc-800 dark:text-zinc-200 text-sm break-words">
                            {{ item.file.name }}
                        </p>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs">
                            {{ formatFileSize(item.file.size) }}
                        </p>
                    </div>
                </div>

                <!-- Database-driven type -->
                <div class="flex flex-col gap-1">
                    <Select v-model="item.type_id" label="Document Type" name="documentType" id="documentType" :options="documentTypes" placeholder="Select documentType" />
                </div>
            </div>
        </div>

        <p v-else class="py-4 text-zinc-400 text-sm text-center">
            No files selected yet.
        </p>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    documentTypes: {
        type: Array,
        default: () => [],
    },
    maxFiles: {
        type: Number,
        default: 10,
    },
    maxFileSize: {
        type: Number,
        default: 10 * 1024 * 1024,
    },
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const files = ref([])
const errorMessage = ref('')
let nextId = 0

const allowedExtensions = [
    'jpg', 'jpeg', 'png', 'webp', 'gif',
    'pdf',
    'doc', 'docx',
    'xls', 'xlsx',
    'csv', 'txt',
]

const acceptedExtensions = computed(() =>
    allowedExtensions.map(extension => `.${extension}`).join(',')
)

const maxFileSizeLabel = computed(() =>
    `${Math.round(props.maxFileSize / (1024 * 1024))} MB`
)

function extensionOf(filename) {
    return filename.split('.').pop()?.toLowerCase() ?? ''
}

function kindOf(file) {
    const extension = extensionOf(file.name)

    if (file.type.startsWith('image/') &&
        ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(extension)) {
        return 'image'
    }

    if (extension === 'pdf') return 'pdf'
    if (['doc', 'docx'].includes(extension)) return 'word'
    if (['xls', 'xlsx', 'csv'].includes(extension)) return 'spreadsheet'
    if (extension === 'txt') return 'text'

    return 'file'
}

function iconFor(kind) {
    const icons = {
        image: 'bi bi-file-image',
        pdf: 'bi bi-file-earmark-pdf',
        word: 'bi bi-file-earmark-word',
        spreadsheet: 'bi bi-file-earmark-excel',
        text: 'bi bi-file-earmark-text',
        file: 'bi bi-file-earmark',
    }

    return icons[kind] ?? icons.file
}

function createItem(file, type_id = '') {
    return {
        id: ++nextId,
        file,
        type_id: type_id == null ? '' : String(type_id),
        kind: kindOf(file),
        preview: URL.createObjectURL(file),
    }
}

function triggerFilePicker() {
    fileInput.value?.click()
}

function handleFileChange(event) {
    addFiles(event.target.files)
    event.target.value = ''
}

function handleDrop(event) {
    addFiles(event.dataTransfer?.files)
}

function addFiles(selectedFiles) {
    if (!selectedFiles?.length) return

    errorMessage.value = ''
    const accepted = []
    const batch = Array.from(selectedFiles)

    for (const file of batch) {
        if (files.value.length + accepted.length >= props.maxFiles) {
            errorMessage.value = `You can upload up to ${props.maxFiles} files.`
            break
        }

        if (!allowedExtensions.includes(extensionOf(file.name))) {
            errorMessage.value = `${file.name}: unsupported file format.`
            continue
        }

        if (file.size > props.maxFileSize) {
            errorMessage.value = `${file.name} exceeds the ${maxFileSizeLabel.value} limit.`
            continue
        }

        accepted.push(createItem(file))
    }

    if (accepted.length) {
        files.value.push(...accepted)
        emitFiles()
    }
}

function setType(id, type_id) {
    const item = files.value.find(file => file.id === id)
    if (!item) return

    item.type_id = type_id
    emitFiles()
}

function removeFile(id) {
    const index = files.value.findIndex(file => file.id === id)
    if (index === -1) return

    URL.revokeObjectURL(files.value[index].preview)
    files.value.splice(index, 1)
    errorMessage.value = ''
    emitFiles()
}

function removeAll() {
    files.value.forEach(file => URL.revokeObjectURL(file.preview))
    files.value = []
    errorMessage.value = ''
    emitFiles()
}

function emitFiles() {
    emit('update:modelValue', files.value.map(item => ({
        file: item.file,
        type_id: item.type_id,
    })))
}

function formatFileSize(size) {
    if (size < 1024 * 1024) {
        return `${(size / 1024).toFixed(1)} KB`
    }

    return `${(size / (1024 * 1024)).toFixed(2)} MB`
}

// Accept initial or externally updated file selections.
watch(
    () => props.modelValue,
    (items) => {
        const incoming = items ?? []

        // Do not recreate object URLs for our own emitted updates.
        if (
            incoming.length === files.value.length &&
            incoming.every((entry, index) =>
                entry.file === files.value[index].file &&
                String(entry.type_id ?? '') === files.value[index].type_id
            )
        ) {
            return
        }

        files.value.forEach(item => URL.revokeObjectURL(item.preview))

        files.value = incoming
            .filter(entry => entry?.file instanceof File)
            .slice(0, props.maxFiles)
            .map(entry => createItem(entry.file, entry.type_id))
    },
    { immediate: true },
)

onBeforeUnmount(() => {
    files.value.forEach(item => URL.revokeObjectURL(item.preview))
})
</script>
