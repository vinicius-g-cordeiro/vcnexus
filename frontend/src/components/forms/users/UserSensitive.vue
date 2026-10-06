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
                <Input v-model="form.socialname" type="text" label="Social Name" name="socialname" id="socialname" />
                <Select v-model="form.marital_status_id" label="Marital Status" name="marital_status_id" id="marital_status_id" :options="maritalOptions" placeholder="Select" />
                <Select v-model="form.religion_id" label="Religion" name="religion_id" id="religion_id" :options="religionOptions" placeholder="Select" />
                <Select v-model="form.ethnicity_id" label="Ethnicity" name="ethnicity_id" id="ethnicity_id" :options="ethnicityOptions" placeholder="Select" />
                <Select v-model="form.nationality_id" label="Nationality" name="nationality_id" id="nationality_id" :options="nationalityOptions" placeholder="Select" />
                <Select v-model="form.sexual_orientation" label="Sexual Orientation" name="sexual_orientation" id="sexual_orientation" :options="sexualOptions" placeholder="Select" />
                <Select v-model="form.gender_id" label="Gender" name="gender_id" id="gender_id" :options="genderOptions" placeholder="Select" />
                <Select v-model="form.disability_id" label="Disability" name="disability_id" id="disability_id" :options="disabilityOptions" placeholder="Select" />
                
            </div>

            <div v-if="mode === 'edit'" class="flex justify-end pt-2">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, watch, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import { useReferencesStore } from '@/stores/references/referencesStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'


const props = defineProps({
    sensitive: { type: Object, default: () => ({}) },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()
const referencesStore = useReferencesStore()

const maritalOptions = ref([])
const religionOptions = ref([])
const ethnicityOptions = ref([])
const nationalityOptions = ref([])
const sexualOptions = ref([])
const genderOptions = ref([])
const disabilityOptions = ref([])

onMounted(async () => {
    const marital_status_ok = await referencesStore.fetchMaritalStatuses();
    if(marital_status_ok === true){
        maritalOptions.value = referencesStore.marital_statuses.map(m => ({ label: m.label, value: String(m.id) }))
    }

    const religions_ok = await referencesStore.fetchReligions();
    if(religions_ok === true){
        religionOptions.value = referencesStore.religions.map(r => ({ label: r.label, value: String(r.id) }))
    }

    const gender_ok = await referencesStore.fetchGenders();
    if(gender_ok === true){
        genderOptions.value = referencesStore.genders.map(g => ({ label: g.label, value: String(g.id) }))
    }

    const ethnicity_ok = await referencesStore.fetchEthnicities();
    if(ethnicity_ok === true){
        ethnicityOptions.value = referencesStore.ethnicities.map(e => ({ label: e.label, value: String(e.id) }))
    }

    const nationality_ok = await referencesStore.fetchNationalities();
    if(nationality_ok === true){
        nationalityOptions.value = referencesStore.nationalities.map(n => ({ label: n.label, value: String(n.id) }))
    }

    const sexual_orientation_ok = await referencesStore.fetchSexualOrientations();
    if(sexual_orientation_ok === true){
        sexualOptions.value = referencesStore.sexual_orientations.map(s => ({ label: s.label, value: s.label }))
    }

    const disability_ok = await referencesStore.fetchDisabilities();
    if(disability_ok === true){
        disabilityOptions.value = referencesStore.disabilities.map(d => ({ label: d.label, value: String(d.id) }))
    }

})

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

function seedFromSensitive(val) {
    form.socialname = val?.socialname ?? ''
    form.gender_id = val?.gender_id ?? ''
    form.religion_id = val?.religion_id ?? ''
    form.ethnicity_id = val?.ethnicity_id ?? ''
    form.nationality_id = val?.nationality_id ?? ''
    form.marital_status_id = val?.marital_status_id != null ? String(val.marital_status_id) : ''
    form.sexual_orientation = val?.sexual_orientation ?? ''
    form.disability_id = val?.disability_id ?? ''
}

if (props.mode === 'create') {
    seedFromSensitive(props.sensitive)
    watch(form, (val) => emit('update:modelValue', { ...val }), { deep: true })
} else {
    watch(() => props.sensitive, seedFromSensitive, { immediate: true })
}



async function handleSubmit() {
    if (props.mode !== 'edit') return
    await userStore.updateSensitive(route.params.uuid, { ...form })
}
</script>