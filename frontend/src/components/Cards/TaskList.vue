<template>
    <section class="rounded-lg border bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800 divide-y divide-neutral-100 dark:divide-neutral-800">
        <div
            v-for="task in tasks"
            :key="task.title"
            class="flex items-center gap-3 px-4 py-3"
        >
            <input
                type="checkbox"
                :checked="task.done"
                class="h-4 w-4 rounded border-neutral-300 dark:border-neutral-600 text-neutral-900 dark:text-neutral-100 shrink-0"
                disabled
            />

            <div class="min-w-0 flex-1">
                <p
                    class="text-sm truncate"
                    :class="task.done
                        ? 'text-neutral-400 line-through'
                        : 'text-neutral-800 dark:text-neutral-100'"
                >
                    {{ task.title }}
                </p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500">
                    {{ task.owner }} &middot; due {{ task.due }}
                </p>
            </div>

            <span
                class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium"
                :class="priorityClass(task.priority)"
            >
                {{ task.priority }}
            </span>
        </div>
    </section>
</template>

<script setup>
defineProps({
    tasks: { type: Array, required: true },
})

function priorityClass(priority) {
    switch (priority) {
        case 'High':
            return 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300'
        case 'Medium':
            return 'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-300'
        default:
            return 'bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400'
    }
}
</script>
