<template>
    <span :aria-disabled="deactivated" tabindex="0" role="menuitem" class="block rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-olive-wood-500" @click="handleClick"
    :class="{ 'bg-zinc-100 dark:bg-zinc-700/60': isActive, 'opacity-50 cursor-not-allowed pointer-events-none': deactivated }">
        <template v-if="to">
            <RouterLink :to="to" :class="linkClasses">
                <slot name="icon"></slot>
                <slot name="label"></slot>
            </RouterLink>
        </template>
        <template v-else>
            <a :href="href" :class="linkClasses">
                <slot name="icon"></slot>
                <slot name="label"></slot>
            </a>
        </template>
    </span>
</template>

<script setup="js">
import { RouterLink } from 'vue-router'
import { ref, computed, defineProps, defineEmits } from 'vue'

const props = defineProps({
    to: {
        type: Object,
    },
    label: {
        type: String,
        required: true
    },
    icon: {
        type: String,
    },
    href: {
        type: String,
        default: '#'
    },
    deactivated: {
        type: Boolean,
        default: false
    },
    danger: {
        type: Boolean,
        default: false
    }
});

const isActive = ref(false)
const emit = defineEmits(['click'])

const linkClasses = computed(() => [
    'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors',
    props.danger
        ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10'
        : 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/60'
])

const handleClick = (e) => {
    isActive.value = !isActive.value
    emit('click', e)
}


</script>