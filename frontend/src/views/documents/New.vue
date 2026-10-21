<template>
    <div class="flex md:flex-row flex-col gap-4 bg-zinc-100 dark:bg-zinc-950 min-h-screen">
        <aside class="flex-shrink-0 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 md:border-r w-full md:w-64">
            <div class="p-4 border-zinc-200 dark:border-zinc-800 border-b">
                <h1 class="font-semibold text-zinc-900 dark:text-zinc-100 text-base">New Document</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs">Fill in and save once</p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <button v-for="section in sections" :key="section.name" type="button" @click="selectedSection = section.name" :class="['flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-left transition-colors', selectedSection === section.name ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' ]">
                    <i :class="['bi', section.icon, 'text-base']"></i>
                    {{ section.label }}
                    <i v-if="section.required && !sectionComplete[section.name]" class="ms-auto text-amber-500 text-xl bi bi-dot"></i>
                </button>
            </nav>

        </aside>
            <main class="flex-1 p-4 sm:p-6">
                <div v-if="submitError" class="flex items-start gap-2 bg-red-50 dark:bg-red-950/40 mb-4 p-3 border border-red-200 dark:border-red-900 rounded-lg max-w-2xl text-red-700 dark:text-red-400 text-sm">
                    <i class="mt-0.5 bi bi-exclamation-triangle"></i>
                    <span>{{ submitError }}</span>
                </div>

                <Files v-if="selectedSection === 'files'" v-model="draft.documents" :document-types="documentTypeOptions" :documents="draft.documents" :max-files="10" :max-file-size="10485760" />

                            <div class="flex items-center gap-3 mt-6 max-w-2xl">
                <button type="button" :disabled="submitting" @click="handleCreate" class="flex items-center gap-2 bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 disabled:opacity-50 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    <i v-if="submitting" class="animate-spin bi bi-arrow-repeat"></i>
                    {{ submitting ? 'Creating...' : 'Save' }}
                </button>
            </div>
            </main>

        </div>
</template>

<script setup lang="js">
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useReferencesStore } from '@/stores/references/referencesStore'
import { useDocumentsStore } from '@/stores/documents/documentsStore'
import Files from '@/components/forms/documents/Files.vue'

const referencesStore = useReferencesStore()
const documentsStore = useDocumentsStore()

const documentTypeOptions = computed(() => referencesStore.document_types.map(t => ({ label: t.label, value: t.id })))
const sections = ref([
    { name: 'files', label: 'Document', required: false, icon: 'bi bi-file-earmark'}

])
const submitting = ref(false)
const submitError = ref(null)
const selectedSection = ref('files')
const sectionComplete = ref([])
const draft = reactive({
    documents: null
})

onMounted(async() => {
    await referencesStore.fetchDocumentTypeOptions()
})


async function handleCreate() {
    submitError.value = ''
    submitting.value = true
    try {
        const payload = new FormData()

        draft.documents.forEach((document, index) => {
            console.log(index, document.file)
            payload.append(`documents[${index}][file]`, document.file)
            payload.append(`documents[${index}][type_id]`, document.type_id)
        })

        const { uuid } = await documentsStore.createDocument(payload)
        router.push({ name: 'documents.list', params: { uuid } })
    } catch (err) {
        submitError.value = err?.response?.data?.message ?? 'Failed to create documents. Please check the required fields and try again.'
    } finally {
        submitting.value = false
    }
}

</script>
