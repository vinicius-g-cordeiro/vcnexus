<template>

    <main class="bg-zinc-50 dark:bg-zinc-900 min-h-screen">
        <Header :nav-items="[
            { label: t('header.links.home'), href: '/', icon: 'bi bi-house-fill' },
            { label: t('header.links.about'), href: '/', icon: 'bi bi-house-fill' },
           
        ]" user-name="Vinicius Cordeiro" @search="onSearch">
            <template v-slot:brand>
                VCNexus
            </template>
        </Header>

        <router-view />
    </main>
</template>

<script setup>
import Header from '@/components/Header.vue';

import { useI18n } from 'vue-i18n'
const { t } = useI18n()

</script>

<style></style>