<template>
    <section class="rounded-lg border bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800 p-4">
        <ol class="relative border-l border-neutral-200 dark:border-neutral-800 ml-1.5">
            <li
                v-for="event in events"
                :key="event.title + event.time"
                class="pl-4 pb-4 last:pb-0 relative"
            >
                <span
                    class="absolute -left-[5px] top-1 h-2.5 w-2.5 rounded-full ring-2 ring-white dark:ring-neutral-900"
                    :class="dotClass(event.type)"
                ></span>

                <p class="text-sm text-neutral-800 dark:text-neutral-100">
                    <span class="font-medium">{{ event.actor }}</span>
                    {{ event.title }}
                </p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">
                    {{ event.time }}
                </p>
            </li>
        </ol>
    </section>
</template>

<script setup>
defineProps({
    events: { type: Array, required: true },
})

function dotClass(type) {
    switch (type) {
        case 'error':
            return 'bg-rose-500'
        case 'warning':
            return 'bg-amber-500'
        case 'success':
            return 'bg-emerald-500'
        default:
            return 'bg-sky-500'
    }
}
</script>
