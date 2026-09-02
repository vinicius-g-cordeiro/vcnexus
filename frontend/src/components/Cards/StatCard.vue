<template>
    <section class="rounded-lg border p-4 bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800">
        <div class="flex items-center justify-between">
            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ label }}</p>
            <i :class="['bi', icon, iconClass, 'text-sm']"></i>
        </div>
        <p class="mt-2 text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-50">
            {{ value }}
        </p>
        <p
            v-if="delta"
            class="mt-1 text-xs flex items-center gap-1"
            :class="deltaDirection === 'down' ? 'text-rose-500' : 'text-emerald-500'"
        >
            <i :class="['bi', deltaDirection === 'down' ? 'bi-arrow-down-right' : 'bi-arrow-up-right']"></i>
            {{ delta }}
            <span class="text-neutral-400 dark:text-neutral-500">vs last month</span>
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
    neutral: 'text-neutral-400',
}

const iconClass = computed(() => iconColors[props.accent] ?? iconColors.neutral)
</script>
