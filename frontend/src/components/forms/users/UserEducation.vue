<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-3xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Education</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Educational background</p>
            </div>
            <button type="button" @click="addEducation" class="flex items-center gap-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-1.5 rounded-md font-medium text-zinc-700 dark:text-zinc-300 text-sm transition-colors">
                <i class="bi bi-plus-lg"></i> Add
            </button>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <p v-if="loading" class="py-10 text-zinc-400 text-sm text-center">Loading...</p>

            <template v-else>
                <div v-for="(edu, i) in form.list" :key="edu._key" class="relative bg-zinc-50 dark:bg-zinc-800/40 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <button type="button" @click="removeEducation(i)" class="top-3 right-3 absolute text-zinc-400 hover:text-red-500 transition-colors">
                        <i class="bi bi-trash"></i>
                    </button>

                    <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-1">
                        <Select label="Type" :id="`type-${edu._key}`" :options="typeOptions" v-model="edu.educational_type_id" placeholder="Select type" required @change="onTypeChange(edu), onEducationTypeChange(edu)"  />
                    </div>
                    <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-1">
                        <Input v-model="edu.institution" label="Institution" :id="`institution-${edu._key}`" required />
                        
                    </div>
                    
                    <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2" v-if="edu.educational_type_id === 1 || edu.educational_type_id === 2">
                        <Select v-model="edu.completion_status_id" v-if="showsLevel(edu)" label="Completion" :id="`completion-${edu._key}`" @change="onInProgressChange(edu)" :options="completionOptions" placeholder="Select" required />

                        <Select v-if="showsLevel(edu)" label="Educational Level" :id="`level-${edu._key}`" :options="levelOptions" v-model="edu.educational_level_id" placeholder="Select level" required />
                    </div>

                    <div class="flex flex-col gap-x-4">
                        <Select v-model="edu.completion_status_id" label="Completion" :id="`completion-${edu._key}`" @change="onInProgressChange(edu)" :options="completionOptions" placeholder="Select" required />
                    </div>

                    <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">

                        <Input v-model="edu.start_date" type="date" label="Start Date" :id="`start-${edu._key}`" required />

                        <Input v-model="edu.end_date" type="date" label="End Date" :id="`end-${edu._key}`" v-if="edu.completion_status_id === 2 || edu.completion_status_id === 3 || edu.completion_status_id === 7 || edu.completion_status_id === 5" />

                        <Input v-model="edu.expiration_date" type="date" label="Expiration Date" :id="`expiration-${edu._key}`" v-if="edu.educational_type_id === 3 || edu.educational_type_id === 4" />
                    </div>

                    <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-1" v-if="showsLevel(edu) && edu.educational_type_id === 2">
                        <Input v-model="edu.degree" :label="degreeFieldLabel(edu)" :placeholder="degreeFieldPlaceholder(edu)" :id="`degree-${edu._key}`" />
                    </div>
                    <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2" v-if="showsLevel(edu) === false">

                        <Input v-model="edu.certification_url" label="Certification URL" :id="`url-${edu._key}`" />
                        <Input v-model="edu.certification_number" label="Credential ID" placeholder="e.g. ABC-123456" :id="`credential-${edu._key}`" />
                    </div>

                    <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-1">
                        <Input type="textarea" v-model="edu.description" label="Description" :id="`description-${edu._key}`" />
                    </div>
                </div>

                <p v-if="!form.list.length" class="py-6 text-zinc-400 text-sm text-center">No education added yet</p>
            </template>

            <div v-if="mode === 'edit'" class="flex justify-end pt-2">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import { useReferencesStore } from '@/stores/references/referencesStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    education: { type: Array, default: () => [] },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()
const referencesStore = useReferencesStore()

// 1: school, 2: certificate/course, 3: degree — per the spec given
const typeOptions = computed(() => referencesStore.educational_types.map(t => ({ label: t.label, value: t.id })))
//'0 = Not Completed, 1 = Completed, 2= In Progress, 3 = On Hold, 4 = Other'
const completionOptions = computed(() => referencesStore.completion_statuses.map(s => ({ label: s.label, value: s.id })))


const levelOptions = ref([])
const loadingLevels = ref(true)

const form = reactive({ list: [] })

let nextKey = 0
function makeRowKey() {
    return nextKey++
}

function onEducationTypeChange(edu){
    // change education levels to match the selected type
    levelOptions.value = referencesStore.educational_levels.filter((el) => el.educational_type_id === edu.educational_type_id).map(el => ({ label: el.label, value: el.id }))
}

function formatDate(date){
    if(date === undefined || date===null) return ''
    date = new Date(date)
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')

    return `${year}-${month}-${day}`
}
function toRow(raw) {
    return {
        type: raw.type ?? '',
        educational_level_id: raw.educational_level_id ?? '',
        institution: raw.institution ?? '',
        degree: raw.degree ?? '',
        certification_url: raw.certification_url ?? '',
        educational_type_id: raw.educational_type_id ?? '',
        description: raw.description ?? '',
        certification_status_id: raw.certification_status_id ?? '',
        certification_number: raw.certification_number ?? '',
        start_date: formatDate(raw.start_date), // convert the datetime to mm-dd-yyyy
        end_date: formatDate(raw.end_date) ,
        expiration_date: raw.expiration_date ?? '',
        completion_status_id: raw.completion_status_id ?? '',
        _key: makeRowKey(),
    }
}

function toPayload(row) {
    const { _key, completion, ...rest } = row
    const payload = { ...rest, end_date: completion_status_id ? null : rest.end_date }
    if (row.type === 1) {
        // school: no degree/certification name, no credential id — level DOES apply
        payload.degree = null
        payload.certification_number = null
    } else if (row.type === 2) {
        // certificate/course: no educational level
        payload.educational_level_id = null
    } else if (row.type === 3) {
        // degree: no credential id, that's a certificate-only concept
        payload.certification_number = null
    }
    return payload
}

// type 1 = school, 3 = degree both carry an educational level; only
// 2 = certificate/course does not, per spec
function showsLevel(edu) {
    return edu.educational_type_id === 1 || edu.educational_type_id === 2
}

function degreeFieldLabel(edu) {
    return edu.educational_type_id === 3 || edu.educational_type_id === 4 ? 'Certification Name' : 'Degree'
}

function degreeFieldPlaceholder(edu) {
    return edu.educational_type_id === 3 || edu.educational_type_id === 4 ? 'AWS Certified Solutions Architect...' : 'B.Sc. Computer Science...'
}

// clears fields that don't apply to the newly selected type, so a value
// typed in before switching type doesn't linger invisibly and get submitted
function onTypeChange(edu) {
    if (edu.educational_type_id === 1) {
        edu.degree = ''
        edu.certification_number = ''
    } else if (edu.educational_type_id === 2) {
        edu.educational_level_id = ''
    } else if (edu.educational_type_id === 3) {
        edu.certification_number = ''
    }
}

const seeded = ref(false)

function seedFromEducation(val) {
    form.list = (val ?? []).map(toRow)
    seeded.value = true
}

if (props.mode === 'create') {
    watch(() => props.education, (val) => {
        if (seeded.value) return
        seedFromEducation(val ?? [])
    }, { immediate: true })
    watch(form, (val) => emit('update:modelValue', val.list.map(toPayload)), { deep: true })
} else {
    watch(() => props.education, (val) => seedFromEducation(val ?? []), { immediate: true })
}

const loading = computed(() => loadingLevels.value || (props.mode === 'create' && !seeded.value))

function addEducation() {
    form.list.push(toRow({}))
}

function removeEducation(index) {
    form.list.splice(index, 1)
}

function onInProgressChange(edu) {
    switch (edu.completion) {
        case 0:
            edu.end_date = ''
            break
        case 1:
        case 3:
            edu.end_date = null
            break
    }

}

async function handleSubmit() {
    if (props.mode !== 'edit') return
    await userStore.updateEducation(route.params.uuid, form.list.map(toPayload))
}

onMounted(async () => {
    loadingLevels.value = true
    try {
        const ok = await referencesStore.fetchEducationalLevels()
        if (ok === true) {
            levelOptions.value = (referencesStore.educational_levels ?? [])
                .slice()
                .sort((a, b) => (a.priority ?? 0) - (b.priority ?? 0))
                .map(l => ({
                    label: l.label,
                    value: l.id,
                }))
        }

        const ok_2 = await referencesStore.fetchEducationalTypes()
        if (ok_2 === true) {
            typeOptions.value = (referencesStore.educational_types ?? [])
                .slice()
                .map(t => ({
                    label: t.label,
                    value: t.id,
                }))
        }

        const ok3 = await referencesStore.fetchCompletionStatuses(1)
        if (ok3 === true) {
            completionOptions.value = (referencesStore.completion_statuses ?? [])
                .slice()
                .map(c => ({
                    label: c.label,
                    value: c.id,
                }))
        }
    } finally {
        loadingLevels.value = false
    }
})
</script>