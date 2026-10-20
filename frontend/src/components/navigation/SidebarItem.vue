<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useSidebar } from '@/composables/useSidebar'

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const route = useRoute()
const { isOpen } = useSidebar()

const hasChildren = computed(() => props.item.children?.length > 0)

function containsActive(item) {
    return item.route === route.name || (item.children ?? []).some(containsActive)
}

const isActive = computed(() => containsActive(props.item))
const isExpanded = ref(hasChildren.value && isActive.value)

watch(isActive, (active) => {
    if (active && hasChildren.value) isExpanded.value = true
})

const baseClasses = computed(() => [
    'flex w-full items-center gap-3 rounded-lg py-2 text-sm font-medium transition-colors duration-200 outline-none focus-visible:ring-2 focus-visible:ring-olive-wood-500',
    isOpen.value ? 'px-3' : 'justify-center px-0'
])
const activeClasses = 'bg-olive-wood-500 text-white shadow-sm'
const idleClasses = 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white'
</script>

<template>
    <li>
        <RouterLink
            v-if="!hasChildren"
            :to="{ name: item.route }"
            :title="item.label"
            :class="[baseClasses, isActive ? activeClasses : idleClasses]"
        >
            <i class="w-5 text-base text-center shrink-0 bi" :class="item.icon"></i>
            <span v-if="isOpen" class="truncate">{{ item.label }}</span>
        </RouterLink>

        <template v-else>
            <button
                type="button"
                :title="item.label"
                :aria-expanded="isExpanded"
                :class="[baseClasses, isActive ? activeClasses : idleClasses]"
                @click="isExpanded = !isExpanded"
            >
                <i class="w-5 text-base text-center shrink-0 bi" :class="item.icon"></i>
                <span v-if="isOpen" class="flex-1 text-left truncate">{{ item.label }}</span>
                <i v-if="isOpen" class="text-xs transition-transform duration-200 bi bi-chevron-down" :class="{ 'rotate-180': isExpanded }"></i>
            </button>

            <ul v-show="isExpanded" class="flex flex-col gap-1 mt-1" :class="{ 'ml-5 pl-2 border-l border-zinc-200 dark:border-zinc-700': isOpen }">
                <SidebarItem v-for="child in item.children" :key="child.id" :item="child" />
            </ul>
        </template>
    </li>
</template>