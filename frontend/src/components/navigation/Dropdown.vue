<template>
    <div class="inline-block z-50 relative text-left">
        <slot name="trigger" :toggle="toggleDropdown" :isOpen="isDropdownOpen" />

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75"
        leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-if="isDropdownOpen" class="right-0 absolute mt-2 w-60 origin-top-right">
                <div class="bg-white dark:bg-zinc-800 shadow-xl border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden text-zinc-900 dark:text-zinc-100">
                    <slot name="header" />
                    <div class="p-1">
                        <slot name="content" :close="toggleDropdown" />
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup="js">
import { ref } from 'vue'

const isDropdownOpen = ref(false)

const props = defineProps({
    authStore: {
        type: Object,
        required: true
    }
})

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}


</script>