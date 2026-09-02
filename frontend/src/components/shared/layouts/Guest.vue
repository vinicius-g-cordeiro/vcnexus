<template>

    <main class="bg-neutral-50 dark:bg-neutral-900 min-h-screen">
        <Header :nav-items="[
            { label: 'Home', href: '/' , icon: 'bi bi-house-fill'},
            { label: 'About', href: '/about', icon: 'bi bi-info-square-fill' },
           
        ]" user-name="Vinicius Cordeiro" @search="onSearch">
            <template v-slot:brand>
                VCNexus
            </template>
        </Header>

        <router-view />
    </main>
</template>

<script setup>
import Header from '@/components/Header/Header.vue';


</script>

<style></style>