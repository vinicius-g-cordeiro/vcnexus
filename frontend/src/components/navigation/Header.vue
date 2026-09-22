<template>
    <nav class="bg-white dark:bg-stone-800 px-4 lg:px-6 py-2.5 border-stone-200">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
            <div class="flex items-center">
                <span class="self-center font-semibold dark:text-white text-xl whitespace-nowrap">
                    <RouterLink to="/">
                        Home
                    </RouterLink>
                </span>
            </div>

            <div class="flex items-center">
                <DarkmodeButton />
            </div>
        </div>
    </nav>
</template>

<script setup>
import DarkmodeButton from '@/components/DarkmodeButton.vue'

</script>

<style>

</style>