<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { useSidebar } from '@/composables/useSidebar'
import SidebarItem from '@/components/navigation/SidebarItem.vue'

const props = defineProps({
    appName: {
        type: String,
        default: 'VCNexus'
    },
    items: {
        type: Array,
        default: () => []
    }
})

const router = useRouter()
const authStore = useAuthStore()
const { isOpen, toggle } = useSidebar()

function canAccess(item) {
    if (!item.permissions || item.permissions.length === 0) return true
    return authStore.isAuthenticated && authStore.hasPermissions(item.permissions)
}

function filterMenus(list) {
    return [...list]
        .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
        .map((item) => ({ ...item, children: filterMenus(item.children ?? []) }))
        .filter((item) => {
            if (item.children.length > 0) return true
            return Boolean(item.route) && router.hasRoute(item.route) && canAccess(item)
        })
}

const visibleItems = computed(() => filterMenus(props.items))
</script>

<template>
    <aside
        class="top-0 sticky flex flex-col gap-4 bg-zinc-50 dark:bg-zinc-900 p-3 border-zinc-200 dark:border-zinc-800 border-r h-screen overflow-y-auto transition-all duration-300 shrink-0"
        :class="isOpen ? 'w-64' : 'w-[4.5rem]'"
    >
        <div class="flex items-center gap-2 px-1 h-10" :class="isOpen ? 'justify-between' : 'flex-col justify-center h-auto gap-3'">
            <div class="flex items-center gap-2 min-w-0">
                <img class="w-9 h-9 shrink-0" src="@/assets/logo.png" alt="logo">
                <h1 v-show="isOpen" class="font-bold text-olive-wood-500 text-lg truncate tracking-tight">
                    {{ appName }}
                </h1>
            </div>
            <button
                type="button"
                class="flex justify-center items-center hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-lg w-8 h-8 text-zinc-500 dark:text-zinc-400 transition shrink-0"
                :aria-label="isOpen ? 'Collapse sidebar' : 'Expand sidebar'"
                @click="toggle"
            >
                <i class="bi" :class="isOpen ? 'bi-layout-sidebar' : 'bi-layout-sidebar-reverse'"></i>
            </button>
        </div>

        <nav class="flex-1" aria-label="Main">
            <ul class="flex flex-col gap-1">
                <SidebarItem v-for="item in visibleItems" :key="item.id" :item="item" />
            </ul>
        </nav>
    </aside>
</template>