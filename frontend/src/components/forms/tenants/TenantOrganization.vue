<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-3xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Organization</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Organization legal information and subscription plan/statuses</p>
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div class="gap-4 grid grid-cols-1 sm:grid-cols-2">
                <Input v-model="form.fantasy_name" type="text" label="Fantasy Name" name="fantasy_name" id="fantasy_name" required class="w-full" />

                <Input v-model="form.trade_name" type="text" label="Trade Name" name="trade_name" id="trade_name" class="w-full" />

                <Select v-model="form.legal_entity_type_id" type="text" label="Legal Entity Type" name="legal_entity_type_id" class="w-full" :options="legalEntityTypesOptions" required placeholder="Select" />

                <Input v-model="form.slug" type="text" label="Slug" name="slug" id="slug" class="w-full" />

                <Input v-model="form.tax_id" type="text" label="Tax ID" name="tax_id" id="tax_id" class="w-full" />

                <Input v-model="form.state_registration" type="text" label="State Registration" name="state_registration" id="state_registration" class="w-full" />

                <Input v-model="form.municipal_registration" type="text" label="Municipal Registration" name="municipal_registration" id="municipal_registration" class="w-full" />

                <Input v-model="form.domain" type="text" label="Domain" name="domain" id="domain" class="w-full" />

                <Input v-model="form.description" type="text" label="Description" name="description" id="description" class="w-full" />

                <Select v-model="form.subscription_type_id" label="Subscription Plan" name="subscription_type_id" id="subscription_type_id" :options="subscriptionPlansOptions" placeholder="Select" required />

                <Select v-model="form.subscription_status_id" label="Subscription Status" name="subscription_status_id" id="subscription_status_id" :options="subscriptionStatusesOptions" placeholder="Select" required />
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, computed, onMounted, watch} from 'vue'
import { useReferencesStore } from '@/stores/references/referencesStore'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
    fields: { type: Array, default: () => [] },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const referencesStore = useReferencesStore()

const form = reactive({
    app_name: '',
    fantasy_name: '',
    trade_name: '',
    slug: '',
    tax_id: '',
    state_registration: '',
    municipal_registration: '',
    website: '',
    description: '',
    subscription_type_id: null,
    subscription_status_id: null,
    legal_entity_type_id: null,
})

const subscriptionPlansOptions = computed(() => referencesStore.subscription_plans.map(t => ({ label: t.label, value: t.id })))
const subscriptionStatusesOptions = computed(() => referencesStore.subscription_statuses.map(p => ({ label: p.label, value: p.id })))
const legalEntityTypesOptions = computed(() => referencesStore.legal_entity_types.map(legalType => ({label: legalType.label, value: legalType.id})))

watch(form, (val) => emit('update:modelValue', { ...val }), { deep: true })

onMounted(async () => {
    await referencesStore.fetchSubscriptionPlans()
    await referencesStore.fetchSubscriptionStatuses()
    await referencesStore.fetchLegalEntityTypes()
})


async function handleSubmit() {
    if (props.mode !== 'edit') return
}
</script>
