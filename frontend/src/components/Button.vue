<template>
    <component :is="tag" v-bind="tagProps" :type="isNativeButton ? nativeType : undefined" :disabled="isNativeButton ? disabled || loading : undefined" :aria-disabled="!isNativeButton && (disabled || loading) ? 'true' : undefined" :class="[
        'inline-flex items-center justify-center gap-2 rounded-md font-medium transition-colors select-none',
        'focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-800',
        sizeClass,
        variantClass,
        block ? 'w-full' : '',
        (disabled || loading) ? 'opacity-60 cursor-not-allowed pointer-events-none' : 'cursor-pointer',
    ]" @click="handleClick">
        <svg v-if="loading" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>

        <span v-else-if="$slots.icon || icon" class="[&>svg]:w-4 [&>svg]:h-4 shrink-0">
            <slot name="icon">
                <span v-html="icon" />
            </slot>
        </span>

        <span v-if="$slots.default">
            <slot />
        </span>

        <span v-if="!loading && ($slots['icon-right'] || iconRight)" class="[&>svg]:w-4 [&>svg]:h-4 shrink-0">
            <slot name="icon-right">
                <span v-html="iconRight" />
            </slot>
        </span>
    </component>
</template>

<script setup>
/**
 * Button.vue — single configurable button for the whole system.
 * Renders as a native <button>, an <a> (external redirect), or a
 * router-link (<component :is>, works with vue-router's RouterLink
 * if it's registered globally) — decided automatically from the
 * props you pass, or forced via `as`.
 *
 * REDIRECT / NAVIGATION
 *   href="https://..."      -> renders <a>, opens in same tab (or `target`)
 *   to="/settings"           -> renders RouterLink (vue-router), client-side nav
 *   external                 -> forces <a> even if `to` is set (e.g. full page reload)
 *
 * MODAL
 *   modal="confirm-delete"   -> emits ('open-modal', 'confirm-delete') instead of navigating;
 *                                pair with your Modal.vue's v-model in the parent
 *
 * FORM ROLE
 *   native-type="submit"     -> <button type="submit"> inside a <form> (default: "button")
 *   native-type="reset"
 *
 * STYLE
 *   variant: 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger' | 'link'  (default: 'primary')
 *   size:    'sm' | 'md' | 'lg'                                                 (default: 'md')
 *   block, disabled, loading
 *
 * Usage:
 * <Button @click="save">Save</Button>
 * <Button variant="outline" to="/users">Back to users</Button>
 * <Button variant="danger" modal="confirm-delete" @open-modal="openModal">Delete</Button>
 * <Button native-type="submit" :loading="isSaving">Create account</Button>
 * <Button variant="ghost" href="https://docs.example.com" target="_blank">Docs</Button>
 */
import { computed } from 'vue'

const props = defineProps({
    // Navigation
    href: { type: String, default: '' },       // renders <a>
    to: { type: [String, Object], default: '' }, // renders RouterLink
    external: { type: Boolean, default: false }, // force <a> even when `to` is set
    target: { type: String, default: '' },       // e.g. '_blank'

    // Modal trigger — doesn't navigate, just emits so the parent can open a Modal.vue
    modal: { type: String, default: '' },

    // Native <button> behavior (ignored when href/to/modal is used)
    nativeType: {
        type: String,
        default: 'button', // 'button' | 'submit' | 'reset'
        validator: (v) => ['button', 'submit', 'reset'].includes(v),
    },

    // Force the rendered element instead of auto-detecting
    as: { type: String, default: '' }, // 'button' | 'a' | component name

    // Style
    variant: {
        type: String,
        default: 'primary', // 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger' | 'link'
    },
    size: {
        type: String,
        default: 'md', // 'sm' | 'md' | 'lg'
    },
    block: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },

    // Icons — pass a raw SVG string, or use the #icon / #icon-right slots for full control
    icon: { type: String, default: '' },
    iconRight: { type: String, default: '' },
})

const emit = defineEmits(['click', 'open-modal'])

const tag = computed(() => {
    if (props.as) return props.as
    if (props.modal) return 'button'
    if (props.to && !props.external) return 'router-link'
    if (props.href || (props.to && props.external)) return 'a'
    return 'button'
})

const isNativeButton = computed(() => tag.value === 'button')

const tagProps = computed(() => {
    if (tag.value === 'router-link') {
        return { to: props.to }
    }
    if (tag.value === 'a') {
        return {
            href: props.href || (typeof props.to === 'string' ? props.to : undefined),
            target: props.target || undefined,
            rel: props.target === '_blank' ? 'noopener noreferrer' : undefined,
        }
    }
    return {}
})

function handleClick(e) {
    if (props.disabled || props.loading) {
        e.preventDefault()
        return
    }
    if (props.modal) {
        emit('open-modal', props.modal)
    }
    emit('click', e)
}

const sizeClass = computed(() => {
    const map = {
        sm: 'text-xs px-3 py-1.5',
        md: 'text-sm px-4 py-2.5',
        lg: 'text-base px-5 py-3',
    }
    return map[props.size] ?? map.md
})

const variantClass = computed(() => {
    const map = {
        primary:
            'bg-emerald-500 text-white hover:bg-emerald-600 focus:ring-emerald-500',
        secondary:
            'dark:bg-zinc-700 bg-zinc-300 dark:text-zinc-50 text-zinc-900 hover:dark:bg-zinc-600 hover:bg-zinc-400 focus:ring-emerald-500',
        outline:
            'border dark:border-zinc-600 border-zinc-400 dark:text-zinc-50 text-zinc-900 hover:bg-zinc-300 dark:hover:bg-zinc-700 focus:ring-emerald-500',
        ghost:
            'dark:text-zinc-50 text-zinc-900 hover:bg-zinc-300 dark:hover:bg-zinc-700 focus:ring-emerald-500',
        danger:
            'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500',
        link:
            'text-emerald-500 hover:text-emerald-400 underline-offset-4 hover:underline focus:ring-emerald-500 !px-0 !py-0',
    }
    return map[props.variant] ?? map.primary
})
</script>