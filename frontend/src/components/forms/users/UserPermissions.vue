<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-2xl">
        <div>
            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Permissions</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Direct permissions granted to this user</p>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-3">
            <label v-for="perm in form.list" :key="perm.slug" class="flex items-start gap-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg transition-colors cursor-pointer">
                <input type="checkbox" v-model="perm.granted" class="mt-0.5 rounded w-4 h-4 accent-zinc-900 dark:accent-zinc-100" />
                <div class="flex flex-col">
                    <span class="font-medium text-zinc-800 dark:text-zinc-200 text-sm">{{ perm.name }}</span>
                    <span v-if="perm.description" class="text-zinc-500 dark:text-zinc-400 text-xs">{{ perm.description }}</span>
                    <span class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500">{{ perm.slug }}</span>
                </div>
            </label>

            <p v-if="!form.list.length" class="py-6 text-zinc-400 text-sm text-center">No permissions available</p>

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

const props = defineProps({
    permissions: { type: Array, default: () => [] }
})

const route = useRoute()
const userStore = useUserStore()

const form = reactive({ list: [] })

watch(() => props.permissions, (val) => {
    form.list = (val ?? []).map(p => ({
        slug: p.slug,
        name: p.name,
        description: p.description ?? '',
        granted: !!p.granted,
    }))
}, { immediate: true })

async function handleSubmit() {
    await userStore.updatePermissions(route.params.uuid, form.list)
}
</script>