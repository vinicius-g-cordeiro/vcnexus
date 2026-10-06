<template>
    <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900 shadow-sm p-6 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-2xl">
        <div>
            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Roles</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Roles assigned to this user within the current tenant</p>
        </div>

        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div ref="rootEl" class="relative flex flex-col gap-1">
                <label class="font-semibold text-zinc-900 dark:text-zinc-50 text-xs">
                    Roles
                    <span v-if="loading" class="font-normal text-zinc-400">(loading...)</span>
                </label>

                <!-- trigger: selected roles render as removable tags, click anywhere
                     else in the box opens the dropdown -->
                <button
                    type="button"
                    @click="open = !open"
                    class="flex flex-wrap items-center gap-1.5 bg-zinc-100 dark:bg-zinc-800 px-2 py-2 border border-olive-wood-500 dark:border-zinc-600 rounded-md focus:outline-olive-wood-500 min-h-[42px] text-left"
                >
                    <span v-if="!selected.length" class="px-1 text-zinc-400 text-xs">Select roles...</span>

                    <span
                        v-for="role in selected"
                        :key="role.id"
                        class="flex items-center gap-1 bg-zinc-900 dark:bg-zinc-100 px-2 py-1 rounded font-medium text-[11px] text-white dark:text-zinc-900"
                    >
                        {{ role.name }}<template v-if="role.organization_name"> ({{ role.organization_name }})</template>
                        <i class="hover:opacity-70 bi bi-x-lg" @click.stop="toggleRole(role)"></i>
                    </span>

                    <i :class="['bi', open ? 'bi-chevron-up' : 'bi-chevron-down', 'ms-auto text-zinc-400 text-xs shrink-0']"></i>
                </button>

                <!-- dropdown -->
                <div v-if="open" class="top-full z-20 absolute inset-x-0 bg-white dark:bg-zinc-900 shadow-lg mt-1 border border-zinc-200 dark:border-zinc-700 rounded-md max-h-80 overflow-hidden">
                    <div class="p-2 border-zinc-200 dark:border-zinc-700 border-b">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search roles..."
                            class="bg-zinc-100 dark:bg-zinc-800 px-2 py-1.5 border border-zinc-300 dark:border-zinc-600 rounded w-full text-xs"
                            @click.stop
                        />
                    </div>

                    <div class="flex flex-col overflow-y-auto">
                        <p v-if="loading" class="py-6 text-zinc-400 text-xs text-center">Loading roles...</p>
                        <p v-else-if="!filteredCatalog.length" class="py-6 text-zinc-400 text-xs text-center">No roles found</p>

                        <label
                            v-for="role in filteredCatalog"
                            :key="role.id"
                            class="flex items-start gap-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 px-3 py-2 transition-colors cursor-pointer select-none"
                        >
                            <input
                                type="checkbox"
                                :checked="isSelected(role)"
                                @change="toggleRole(role)"
                                class="mt-0.5 rounded w-4 h-4 accent-zinc-900 dark:accent-zinc-100 shrink-0"
                            />
                            <span class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-200 text-xs">{{ role.name }}<template v-if="role.organization_name"> ({{ role.organization_name }})</template></span>
                                <span v-if="role.description" class="text-[11px] text-zinc-500 dark:text-zinc-400">{{ role.description }}</span>
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-between items-center bg-zinc-50 dark:bg-zinc-800/60 px-3 py-2 border-zinc-200 dark:border-zinc-700 border-t text-xs">
                        <span class="text-zinc-500 dark:text-zinc-400">{{ selected.length }} selected</span>
                        <button type="button" @click="clearAll" class="text-zinc-500 hover:text-red-500 dark:text-zinc-400">Clear all</button>
                    </div>
                </div>
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
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/users/userStore'
import { useAuthorizationStore } from '@/stores/authorization/authorizationStore'

const props = defineProps({
    roles: { type: Array, default: () => [] }, // the user's current role assignments (edit mode)
    mode: { type: String, default: 'edit' },
})

const emit = defineEmits(['update:modelValue'])

const route = useRoute()
const userStore = useUserStore()
const authorizationStore = useAuthorizationStore()

const rootEl = ref(null)
const open = ref(false)
const search = ref('')
const loading = ref(true)
const catalog = ref([]) // full role catalog for the current tenant, from the authorization module
const selectedIds = ref([]) // plain ref, not wrapped in reactive() — avoids any
                             // ambiguity around deep-watching a reactive object's
                             // field through a getter; a direct ref watch is
                             // unambiguous and fires on every push/splice
const seeded = ref(false) // guards against re-seeding selection from our own echoed prop

onMounted(async () => {
    loading.value = true
    try {
        // roles live in the authorization module/service alongside permissions;
        // RLS already scopes this to the current tenant, so no client-side
        // tenant grouping is needed here
        const ok = await authorizationStore.fetchRoles() ?? []
        if(ok){            
            catalog.value = authorizationStore.roles ?? []
        }
    } finally {
        loading.value = false
    }
    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})

function handleClickOutside(e) {
    if (open.value && rootEl.value && !rootEl.value.contains(e.target)) {
        open.value = false
    }
}

function seedSelected() {
    selectedIds.value = (props.roles ?? []).map(r => r.id)
    seeded.value = true
}

if (props.mode === 'create') {
    // Same failure mode as UserPermissions: the parent's prop is typically backed
    // by a computed merging catalog + draft state, which gets a new reference
    // every time draft changes — including because OUR emit just changed it. A
    // "compare serialized ids" guard is fragile (identity-key mismatches between
    // parent/child reopen the loop silently). The only reliable fix: seed once,
    // the first time the catalog is non-empty, then never read the prop again —
    // after that, local selection and the parent's draft are kept in sync purely
    // by our own toggles -> emit, one direction only, nothing to echo back into.
    watch(catalog, (val) => {
        if (seeded.value) return
        if (!val || !val.length) return
        seedSelected()
    }, { immediate: true })

    // direct ref watch — fires reliably on every push/splice to selectedIds.value,
    // no getter/deep-watch ambiguity. this is the only place create-mode
    // pushes data to the parent; it never reads props.roles again after seeding
    watch(selectedIds, (ids) => {
        emit('update:modelValue', catalog.value.filter(r => ids.includes(r.id)))
    }, { deep: true })
} else {
    watch([catalog, () => props.roles], seedSelected, { immediate: true })
}

const filteredCatalog = computed(() => {
    if (!search.value.trim()) return catalog.value
    const q = search.value.trim().toLowerCase()
    return catalog.value.filter(r => r.name.toLowerCase().includes(q) || r.description?.toLowerCase().includes(q))
})

const selected = computed(() => catalog.value.filter(r => selectedIds.value.includes(r.id)))

function isSelected(role) {
    return selectedIds.value.includes(role.id)
}

function toggleRole(role) {
    const i = selectedIds.value.indexOf(role.id)
    if (i === -1) selectedIds.value.push(role.id)
    else selectedIds.value.splice(i, 1)
}

function clearAll() {
    selectedIds.value = []
}

async function handleSubmit() {
    if (props.mode !== 'edit') return
    await userStore.updateRoles(route.params.uuid, selected.value)
}
</script>