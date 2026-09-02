<template>
    <section class="bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-800 rounded-lg divide-y divide-neutral-100 dark:divide-neutral-800">
        <div
            v-for="task in tasks"
            :key="task.title"
            class="flex items-center gap-3 px-4 py-3"
        >
            <input
                type="checkbox"
                :checked="task.done"
                class="border-neutral-300 dark:border-neutral-600 rounded w-4 h-4 text-zinc-900 dark:text-zinc-100 shrink-0"
                disabled
            />

            <div class="flex-1 min-w-0">
                <p
                    class="text-sm truncate"
                    :class="task.done
                        ? 'text-zinc-400 line-through'
                        : 'text-zinc-800 dark:text-zinc-100'"
                >
                    {{ task.title }}
                </p>
                <p class="text-zinc-400 dark:text-zinc-500 text-xs">
                    {{ task.owner }} &middot; due {{ task.due }}
                </p>
            </div>

            <span
                class="px-2 py-0.5 rounded-full font-medium text-xs shrink-0"
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
            return 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400'
    }
}
</script>
