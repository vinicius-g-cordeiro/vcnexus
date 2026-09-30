<template>
    <main class="flex flex-col gap-2">
        <Header />
        <article class="flex flex-row justify-between items-center gap-2 mx-auto p-2 max-w-screen-xl">
            <router-view class="w-7xl" />
        </article>
    </main>
</template>

<script setup="js">
import Header from '@/components/navigation/Header.vue'

</script>
