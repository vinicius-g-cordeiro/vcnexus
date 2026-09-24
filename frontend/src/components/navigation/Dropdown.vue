<template>
    <div class="inline-block z-10 relative text-left">
        <slot name="trigger" :toggle="toggleDropdown" :isOpen="isDropdownOpen" />

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" 
        leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-if="isDropdownOpen" class="right-0 absolute shadow-lg mt-2 w-56 origin-top-right">
                <div class="bg-stone-100 dark:bg-stone-300 shadow-xs text-stone-900 dark:text-stone-900">
                    <div class="py-1">
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