<template>
    <section class="bg-white dark:bg-zinc-900 p-4 border border-neutral-200 dark:border-neutral-800 rounded-lg">
        <ol class="relative ml-1.5 border-neutral-200 dark:border-neutral-800 border-l">
            <li
                v-for="event in events"
                :key="event.title + event.time"
                class="relative pb-4 last:pb-0 pl-4"
            >
                <span
                    class="top-1 -left-[5px] absolute rounded-full ring-2 ring-white dark:ring-neutral-900 w-2.5 h-2.5"
                    :class="dotClass(event.type)"
                ></span>

                <p class="text-zinc-800 dark:text-zinc-100 text-sm">
                    <span class="font-medium">{{ event.actor }}</span>
                    {{ event.title }}
                </p>
                <p class="mt-0.5 text-zinc-400 dark:text-zinc-500 text-xs">
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
