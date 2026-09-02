<template>
    <section
        class="flex flex-col gap-1 bg-white dark:bg-zinc-900 p-3 border border-neutral-200 dark:border-neutral-800 rounded-lg"
    >
        <header class="flex items-center gap-2 mb-1.5">
            <span
                class="rounded-full w-1.5 h-1.5 shrink-0"
                :class="dotClass"
            ></span>
            <h3 class="font-medium text-zinc-500 dark:text-zinc-400 text-xs">
                {{ title }}
            </h3>
        </header>

        <router-link
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="flex items-center gap-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 px-2 py-1.5 rounded-md text-zinc-700 dark:text-zinc-200 text-sm transition-colors"
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
    neutral: 'bg-zinc-400',
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
    neutral: 'text-zinc-400',
}

const dotClass = computed(() => dotColors[props.accent] ?? dotColors.neutral)
const iconClass = computed(() => iconColors[props.accent] ?? iconColors.neutral)
</script>
