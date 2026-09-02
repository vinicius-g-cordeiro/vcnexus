<template>
    <section
        class="flex flex-col gap-1 rounded-lg border p-3 bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800"
    >
        <header class="flex items-center gap-2 mb-1.5">
            <span
                class="h-1.5 w-1.5 rounded-full shrink-0"
                :class="dotClass"
            ></span>
            <h3 class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                {{ title }}
            </h3>
        </header>

        <router-link
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
        >
            <i :class="['bi', link.icon, iconClass]"></i>
            {{ link.label }}
        </router-link>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: { type: String, required: true },
    accent: { type: String, default: 'neutral' },
    links: { type: Array, required: true },
})

// Small, fixed map keeps Tailwind's compiler able to see the class strings
// (dynamic template interpolation like `bg-${accent}-500` gets purged).
const dotColors = {
    sky: 'bg-sky-500',
    violet: 'bg-violet-500',
    emerald: 'bg-emerald-500',
    amber: 'bg-amber-500',
    indigo: 'bg-indigo-500',
    rose: 'bg-rose-500',
    cyan: 'bg-cyan-500',
    orange: 'bg-orange-500',
    neutral: 'bg-neutral-400',
}

const iconColors = {
    sky: 'text-sky-500',
    violet: 'text-violet-500',
    emerald: 'text-emerald-500',
    amber: 'text-amber-500',
    indigo: 'text-indigo-500',
    rose: 'text-rose-500',
    cyan: 'text-cyan-500',
    orange: 'text-orange-500',
    neutral: 'text-neutral-400',
}

const dotClass = computed(() => dotColors[props.accent] ?? dotColors.neutral)
const iconClass = computed(() => iconColors[props.accent] ?? iconColors.neutral)
</script>
