<script setup>
import { ref, computed, onMounted } from 'vue'
import FilterForm from '@/components/forms/FilterForm.vue'
import { useListing } from '@/composables/useListing'

const filters = ref({})

const fields = [
    { name: 'search', label: 'Search', type: 'text', placeholder: 'Search by name, email or role', classes: 'lg:col-span-2' },
    { name: 'status', label: 'Status', type: 'select', options: [{ value: '1', label: 'Active' }, { value: '0', label: 'Inactive' }] },
    { name: 'blocked', label: 'Blocked', type: 'select', options: [{ value: '1', label: 'Blocked' }, { value: '0', label: 'Not Blocked' }] },
    { name: 'created_at', label: 'Created', type: 'date' },
]

const { items, loading, error, meta, load , clear} = useListing('/users', 10)

const lastPage = computed(() => Math.max(1, Math.ceil(meta.total / meta.perPage)))
const from = computed(() => (meta.total === 0 ? 0 : (meta.page - 1) * meta.perPage + 1))
const to = computed(() => Math.min(meta.page * meta.perPage, meta.total))

function search(values) {
    load(values, 1)
}


function initials(name) {
    return (name || '?').trim().slice(0, 2).toUpperCase()
}

function formatDate(d) {
    return d ? new Date(d).toLocaleDateString('pt-BR') + ' ' + new Date(d).toLocaleTimeString('pt-BR') : ''
}

function getStatus(status) {
    const statuses = {
        1: {
            label: 'Online',
            color: 'bg-emerald-500',
            spanColor: 'bg-emerald-100',
            spanTextColor: 'text-emerald-700',
        },
        2: {
            label: 'Away',
            color: 'bg-amber-500',
            spanColor: 'bg-amber-100',
            spanTextColor: 'text-amber-700',
        },
        3: {
            label: 'Busy',
            color: 'bg-rose-500',
            spanColor: 'bg-rose-100',
            spanTextColor: 'text-rose-700',
        },
        0: {
            label: 'Offline',
            color: 'bg-zinc-500',
            spanColor: 'bg-zinc-100',
            spanTextColor: 'text-zinc-700',
        },
    }

    return statuses[status] || statuses[0]
}
</script>

<template>
    <section class="flex flex-col gap-4 p-4 sm:p-6">
        <header class="flex justify-between items-center">
            <div>
                <h1 class="font-bold text-2xl sm:text-3xl tracking-tight">
                    <span class="text-olive-wood-400">Users</span>
                </h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">{{ meta.total }} total</p>
            </div>
        </header>

        <div class="bg-zinc-100 dark:bg-zinc-800 shadow-sm p-4 rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-700">
            <FilterForm v-model="filters" :fields="fields" @search="search" @reset="clear" />
        </div>

        <div class="bg-zinc-100 dark:bg-zinc-800 shadow-sm rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-700 overflow-hidden">
            <p v-if="error" class="p-6 text-red-600 dark:text-red-400 text-sm">{{ error }}</p>

            <div v-else class="overflow-x-auto" :class="{ 'opacity-50 transition': loading }">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-200/60 dark:bg-zinc-700/40">
                        <tr class="font-semibold text-zinc-500 dark:text-zinc-400 text-xs text-left uppercase tracking-wide">
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Details</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr v-for="item in items" :key="item.id" class="hover:bg-zinc-200/50 dark:hover:bg-zinc-700/30 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex justify-center items-center bg-olive-wood-400/20 rounded-full w-9 h-9 font-semibold text-olive-wood-400 text-xs shrink-0">
                                        {{ initials(item.firstname) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium truncate">{{ item.firstname }}</p>
                                        <p class="text-zinc-500 dark:text-zinc-400 text-xs truncate">{{ item.email }}</p>
                                        <div class="flex flex-wrap gap-1">
                                            <span v-for="r in item.roles || []" :key="r" class="bg-zinc-200 dark:bg-zinc-700 px-2 py-0.5 rounded-md font-medium text-zinc-700 dark:text-zinc-200 text-xs">
                                                {{ r }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="flex flex-col gap-2 px-4 py-3">

                                <p class="items-center rounded-full text-md" >
                                    <b class="text-olive-wood-400">Organization:</b>
                                    <strong class="font-bold">{{ item.organization_name }}</strong> 
                                </p>
                                <span class="flex mb-2 rounded-full font-medium text-xs" >
                                    <b>Created:</b>{{ formatDate(item.created_at) }}
                                </span>
                                <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full font-medium text-xs" :class="getStatus(item.online_status).spanColor + ' ' + getStatus(item.online_status).spanTextColor">
                                    <span class="rounded-full w-1.5 h-1.5" :class="getStatus(item.online_status).color"></span>
                                    {{ getStatus(item.online_status).label }}
                                </span>
                                <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full font-medium text-xs" :class="item.active === 1
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400'
                                    : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300'">
                                    <span class="rounded-full w-1.5 h-1.5" :class="item.active === 1 ? 'bg-emerald-500' : 'bg-zinc-400'"></span>
                                    {{ Number(item.active) === 1 ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full font-medium text-xs" v-if="item.blocked === 1" :class="item.blocked === 1 ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300'">
                                    {{ item.blocked === 1 ? 'Blocked' : 'Not Blocked' }}
                                </span>
                                </div>

                            </td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <RouterLink :to="{ name: 'users.edit', params: { uuid: item.uuid } }" class="text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 dark:text-zinc-400 hover:underline">
                                        <i class="bi bi-pencil-fill"></i>
                                    </RouterLink>

                                    <button type="button" @click="destroy(item.id)" class="text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 dark:text-zinc-400 hover:underline">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!loading && items.length === 0">
                            <td colspan="5" class="px-4 py-10 text-zinc-500 dark:text-zinc-400 text-center">
                                No users found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <footer class="flex justify-between items-center px-4 py-3 border-zinc-200 dark:border-zinc-700 border-t text-sm">
                <p class="text-zinc-500 dark:text-zinc-400">
                    {{ from }}-{{ to }} of {{ meta.total }}
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-zinc-500 dark:text-zinc-400">Page {{ meta.page }} / {{ lastPage }}</span>
                    <button :disabled="meta.page <= 1" class="hover:bg-zinc-200 dark:hover:bg-zinc-700 disabled:opacity-40 px-3 py-1.5 border border-zinc-300 dark:border-zinc-600 rounded-lg font-medium transition disabled:cursor-not-allowed" @click="load(filters, meta.page - 1)">
                        Prev
                    </button>
                    <button :disabled="meta.page >= lastPage" class="hover:bg-zinc-200 dark:hover:bg-zinc-700 disabled:opacity-40 px-3 py-1.5 border border-zinc-300 dark:border-zinc-600 rounded-lg font-medium transition disabled:cursor-not-allowed" @click="load(filters, meta.page + 1)">
                        Next
                    </button>
                </div>
            </footer>
        </div>
    </section>
</template>