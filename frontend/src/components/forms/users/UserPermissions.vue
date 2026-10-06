<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-5xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Permissions</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Direct permissions granted to this user</p>
            </div>
            <div class="flex items-center gap-1 text-zinc-400 text-xs">
                <button type="button" @click="expandAll" class="px-2 py-1 hover:text-zinc-700 dark:hover:text-zinc-200">Expand all</button>
                <span>·</span>
                <button type="button" @click="collapseAll" class="px-2 py-1 hover:text-zinc-700 dark:hover:text-zinc-200">Collapse all</button>
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-3">
            <p v-if="loading" class="py-10 text-zinc-400 text-sm text-center">Loading permissions...</p>

            <!-- columns flow groups top-to-bottom then wrap to the next column, so
                 short groups don't force you to scroll past a tall one to reach them -->
            <div v-else class="gap-3 space-y-3 sm:columns-2 lg:columns-3">
                <div v-for="group in groups" :key="group.key" class="inline-block mb-3 border border-zinc-200 dark:border-zinc-700 rounded-lg w-full overflow-hidden break-inside-avoid">
                    <div class="flex items-center gap-1 bg-zinc-50 dark:bg-zinc-800/60 pr-2 pl-1 w-full">
                        <!-- chevron is the ONLY accordion trigger -->
                        <button type="button" @click="toggleGroup(group.key)" class="flex items-center hover:bg-zinc-100 dark:hover:bg-zinc-800 p-2 rounded-md transition-colors shrink-0">
                            <i :class="['bi', expanded[group.key] ? 'bi-chevron-down' : 'bi-chevron-right', 'text-zinc-400 text-xs']"></i>
                        </button>
                        <!-- checkbox + label together are the toggle-all-in-group control -->
                        <label class="flex flex-1 items-center gap-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 px-1 py-2.5 rounded-md transition-colors cursor-pointer select-none">
                            <input
                                type="checkbox"
                                :checked="group.state === 'all'"
                                :indeterminate.prop="group.state === 'some'"
                                @change="toggleGroupAll(group)"
                                class="rounded w-4 h-4 accent-zinc-900 dark:accent-zinc-100"
                            />
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200 text-sm capitalize">{{ group.label }}</span>
                            <span class="bg-zinc-200 dark:bg-zinc-700 px-1.5 py-0.5 rounded-full font-medium text-[10px] text-zinc-600 dark:text-zinc-300">
                                {{ group.grantedCount }}/{{ group.items.length }}
                            </span>
                        </label>
                    </div>

                    <div v-show="expanded[group.key]" class="flex flex-col gap-1 p-2">
                        <div v-for="perm in group.items" :key="perm.id" class="flex items-start gap-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 p-2.5 rounded-md transition-colors">
                            <input :id="`perm-${perm.id}`" type="checkbox" v-model="perm.granted" class="mt-0.5 rounded w-4 h-4 accent-zinc-900 dark:accent-zinc-100 shrink-0" />
                            <label :for="`perm-${perm.id}`" class="flex flex-col flex-1 cursor-pointer select-none">
                                <span class="font-medium text-zinc-800 dark:text-zinc-200 text-sm">{{ perm.name }}</span>
                                <span v-if="perm.description" class="text-zinc-500 dark:text-zinc-400 text-xs">{{ perm.description }}</span>
                                <span class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500">{{ perm.slug }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <p v-if="!loading && !groups.length" class="py-6 text-zinc-400 text-sm text-center">No permissions available</p>

            <div v-if="mode === 'edit'" class="flex justify-end pt-2">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300 px-4 py-2 rounded-md font-medium text-white dark:text-zinc-900 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'

const props = defineProps({
    permissions: { type: Array, default: () => [] },
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()

const form = reactive({ list: [] })
const expanded = reactive({})

// true once form.list has been populated at least once — guards re-seeding.
// must be a real ref: `loading` below depends on it, and a plain object
// property mutation is invisible to Vue's reactivity system, so `loading`
// would compute once and then never update — exactly the bug that left the
// screen stuck on "Loading permissions..." forever even after data arrived
const seeded = ref(false)

function seedFromPermissions(val) {
    form.list = (val ?? []).map(p => ({
        id: p.id,
        slug: p.slug,
        name: p.name,
        description: p.description ?? '',
        granted: !!p.granted,
    }))
    seeded.value = true
    // default every group open the first time permissions load
    for (const perm of form.list) {
        const key = groupKey(perm.slug)
        if (!(key in expanded)) expanded[key] = true
    }
}

if (props.mode === 'create') {
    // The catalog (permissionsForDisplay in the parent) loads async and is a
    // *computed*, so it gets a brand-new array reference every time draft.permissions
    // changes — including when it changes BECAUSE of our own emit below. That makes
    // "compare incoming vs last snapshot" guards unreliable: the two sides track
    // identity differently (slug here, id in the parent) and any mismatch reopens
    // the loop. The only loop-proof rule: seed from the prop exactly once, the
    // first time real data shows up (form.list still empty, incoming list is not).
    // After that, the parent's merged `granted` state and this component's local
    // `form.list` are kept in sync purely by our own checkbox edits -> emit, never
    // by re-reading the prop — so there is nothing left that can echo.
    watch(() => props.permissions, (val) => {
        if (seeded.value) return
        if (!val || !val.length) return
        seedFromPermissions(val)
    }, { immediate: true })

    watch(form, (val) => {
        emit('update:modelValue', val.list.filter(p => p.granted))
    }, { deep: true })
} else {
    watch(() => props.permissions, seedFromPermissions, { immediate: true })
}

const loading = computed(() => props.mode === 'create' && !seeded.value)

// everything before the first dot in the slug — "users.edit" -> "users",
// a slug with no dot falls into its own "general" bucket instead of crashing
function groupKey(slug) {
    const i = slug.indexOf('.')
    return i === -1 ? 'general' : slug.slice(0, i)
}

const groups = computed(() => {
    const buckets = new Map()
    for (const perm of form.list) {
        const key = groupKey(perm.slug)
        if (!buckets.has(key)) buckets.set(key, [])
        buckets.get(key).push(perm)
    }
    return [...buckets.entries()]
        .sort(([a], [b]) => a.localeCompare(b))
        .map(([key, items]) => {
            const grantedCount = items.filter(p => p.granted).length
            return {
                key,
                label: key.replace(/[_-]/g, ' '),
                items,
                grantedCount,
                state: grantedCount === 0 ? 'none' : grantedCount === items.length ? 'all' : 'some',
            }
        })
})

function toggleGroup(key) {
    expanded[key] = !expanded[key]
}

function expandAll() {
    for (const g of groups.value) expanded[g.key] = true
}

function collapseAll() {
    for (const g of groups.value) expanded[g.key] = false
}

function toggleGroupAll(group) {
    // native checkbox toggled first, then this runs — group.state was computed
    // from the PRE-click granted values, so "was 'all'" means the click just
    // unchecked it, and vice versa
    const nextValue = group.state !== 'all'
    for (const perm of group.items) perm.granted = nextValue
}

async function handleSubmit() {
    if (props.mode !== 'edit') return
    await userStore.updatePermissions(route.params.uuid, form.list)
}
</script>