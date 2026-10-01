<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-2xl">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Sensitive Data</h2>
                <span class="bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 rounded font-medium text-amber-700 dark:text-amber-400 text-xs">Restricted</span>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Only collected where legally justified and consented to</p>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-2">
            <div class="gap-x-4 grid grid-cols-1 sm:grid-cols-2">
                <Input v-model="form.socialname" label="Social Name" name="socialname" id="socialname" />
                <Select v-model="form.marital_status_id" label="Marital Status" name="marital_status_id" id="marital_status_id" :options="maritalOptions" placeholder="Select" />
                <Input v-model="form.gender_id" label="Gender ID" name="gender_id" id="gender_id" />
                <Input v-model="form.religion_id" label="Religion ID" name="religion_id" id="religion_id" />
                <Input v-model="form.ethnicity_id" label="Ethnicity ID" name="ethnicity_id" id="ethnicity_id" />
                <Input v-model="form.nationality_id" label="Nationality ID" name="nationality_id" id="nationality_id" />
                <Input v-model="form.sexual_orientation" label="Sexual Orientation ID" name="sexual_orientation" id="sexual_orientation" />
                <Input v-model="form.disability_id" label="Disability ID" name="disability_id" id="disability_id" />
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    sensitive: { type: Object, default: () => ({}) }
})

const route = useRoute()
const userStore = useUserStore()

const maritalOptions = [
    { value: '1', label: 'Single' },
    { value: '2', label: 'Married' },
    { value: '3', label: 'Divorced' },
    { value: '4', label: 'Widowed' },
    { value: '5', label: 'Stable Union' },
    { value: '6', label: 'Others' },
]

const form = reactive({
    socialname: '',
    gender_id: '',
    religion_id: '',
    ethnicity_id: '',
    nationality_id: '',
    marital_status_id: '',
    sexual_orientation: '',
    disability_id: '',
})

watch(() => props.sensitive, (val) => {
    form.socialname = val?.socialname ?? ''
    form.gender_id = val?.gender_id ?? ''
    form.religion_id = val?.religion_id ?? ''
    form.ethnicity_id = val?.ethnicity_id ?? ''
    form.nationality_id = val?.nationality_id ?? ''
    form.marital_status_id = val?.marital_status_id != null ? String(val.marital_status_id) : ''
    form.sexual_orientation = val?.sexual_orientation ?? ''
    form.disability_id = val?.disability_id ?? ''
}, { immediate: true })

async function handleSubmit() {
    await userStore.updateSensitive(route.params.uuid, { ...form })
}
</script>