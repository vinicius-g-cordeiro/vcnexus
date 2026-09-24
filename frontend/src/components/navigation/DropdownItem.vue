<template>
    <span :disabled="!deactivated" tabindex="0" role="menuitem" class="relative bg-stone-100 dark:bg-stone-800" @click="handleClick" 
    :class="{ ' border-b-olive-wood-500 dark:border-b-olive-wood-500': isActive, 'deactivated cursor-not-allowed': !deactivated }">
        <template v-if="to">
            <RouterLink :to="to" class="flex items-center gap-2 hover:bg-olive-wood-500 px-1.5 py-1.5">
                <slot name="icon"></slot>
                <slot name="label"></slot>
            </RouterLink>
        </template>
        <template v-else>
            <a :href="href" class="flex items-center gap-2 hover:bg-olive-wood-500 px-1.5 py-1.5">
                <slot name="icon"></slot>
                <slot name="label"></slot>
            </a>
        </template>
    </span>
</template>

<script setup="js">
import { RouterLink } from 'vue-router'
import { ref, defineProps, defineEmits } from 'vue'

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
    }
});

const isActive = ref(false)
const emit = defineEmits(['click'])

// Send the click event to the parent component
const handleClick = (e) => {
    isActive.value = !isActive.value
    emit('click', e)
}


</script>