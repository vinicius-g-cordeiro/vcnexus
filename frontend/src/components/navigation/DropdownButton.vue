<template>
    <button type="button" :class="[
        'inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors',
        'dark:text-stone-50 text-stone-900',
        'hover:bg-stone-300 dark:hover:bg-stone-700',
        'focus:outline-none focus:ring-2 focus:ring-olive-wood-500',
    ]" aria-haspopup="true">
        <slot name="avatar">

        </slot>

        <slot name="name">
        </slot>
    </button>
</template>

<script setup>
/**
 * DropdownButton.vue — the clickable trigger for a Dropdown.
 * Content passed via default slot (e.g. avatar + name).
 * Emits a native `click` — bind it to the `toggle` fn provided
 * by <Dropdown>'s #trigger slot.
 */
</script>
