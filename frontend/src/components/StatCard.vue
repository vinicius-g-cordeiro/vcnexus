<template>
    <section class="bg-white dark:bg-zinc-900 p-4 border border-neutral-200 dark:border-neutral-800 rounded-lg">
        <div class="flex justify-between items-center">
            <p class="text-zinc-500 dark:text-zinc-400 text-xs">{{ label }}</p>
            <i :class="['bi', icon, iconClass, 'text-sm']"></i>
        </div>
        <p class="mt-2 font-semibold text-zinc-900 dark:text-zinc-50 text-2xl tracking-tight">
            {{ value }}
        </p>
        <p
            v-if="delta"
            class="flex items-center gap-1 mt-1 text-xs"
            :class="deltaDirection === 'down' ? 'text-rose-500' : 'text-emerald-500'"
        >
            <i :class="['bi', deltaDirection === 'down' ? 'bi-arrow-down-right' : 'bi-arrow-up-right']"></i>
            {{ delta }}
            <span class="text-zinc-400 dark:text-zinc-500">vs last month</span>
        </p>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: String, default: 'bi-graph-up' },
    delta: { type: String, default: '' },
    deltaDirection: { type: String, default: 'up' }, // 'up' | 'down'
    accent: { type: String, default: 'neutral' },
})

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

const iconClass = computed(() => iconColors[props.accent] ?? iconColors.neutral)
</script>
