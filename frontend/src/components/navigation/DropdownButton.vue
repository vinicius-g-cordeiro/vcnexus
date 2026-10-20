<template>
    <button type="button" :class="[
        'inline-flex items-center gap-2 rounded-full py-1 pl-1 pr-3 text-sm font-medium transition-colors',
        'dark:text-zinc-50 text-zinc-900',
        'hover:bg-zinc-100 dark:hover:bg-zinc-800',
        'focus:outline-none focus-visible:ring-2 focus-visible:ring-olive-wood-500',
    ]" aria-haspopup="true">
        <slot name="avatar">

        </slot>

        <span class="hidden sm:inline max-w-32 truncate">
            <slot name="name">
            </slot>
        </span>

        <i class="text-zinc-400 text-xs bi bi-chevron-down"></i>
    </button>
</template>

<script setup>
</script>