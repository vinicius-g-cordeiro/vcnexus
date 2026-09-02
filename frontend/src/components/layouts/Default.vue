<template>

    <main class="bg-zinc-50 dark:bg-zinc-900 min-h-screen">
        <Header :nav-items="[
            { label: t('header.links.home'), href: '/', icon: 'bi bi-house-fill' },
            {
                label: t('header.links.schedule.schedule'), children: [
                    { label: t('header.links.schedule.calendar'), href: '/schedule/calendar/', icon: 'bi bi-calendar-plus' },
                    { label: t('header.links.schedule.list'), href: '/schedule/list/', icon: 'bi bi-list-task' },
                ] , icon:'bi bi-calendar',                
            },
            {
                label: t('header.links.tasks.tasks'), children: [
                    { label:  t('header.links.tasks.new'), href: '/tasks/new/', icon: 'bi bi-plus-square-fill'  },
                    { label: t('header.links.tasks.list'), href: '/tasks/list/', icon: 'bi bi-list-task'  },
                ], icon: 'bi bi-list-task'
            },
            {
                label: t('header.links.users.users'), children: [
                    { label:  t('header.links.users.new'), href: '/users/new/', icon: 'bi bi-person-plus-fill' },
                    { label: t('header.links.users.list'), href: '/users/list/', icon: 'bi bi-list-task' },
                    { label: t('header.links.users.documents'), href: '/users/documents', icon: 'bi bi-file-earmark' },
                    { label: t('header.links.users.reports'), href: '/users/reports/', icon: 'bi bi-file-spreadsheet' },
                ], icon: 'bi bi-people-fill'
            },
            {
                label: t('header.links.entity.entity'),children: [
                    { label:  t('header.links.entity.new'), href: '/entities/new/', icon: 'bi bi-building-add' },
                    { label: t('header.links.entity.list'), href: '/entities/list/', icon: 'bi bi-buildings' },
                    { label: t('header.links.entity.reports'), href: 'entities/report/', icon: 'bi bi-building-fill-exclamation' },
                ], icon: 'bi bi-building-fill'
            },
        ]" user-name="" @search="onSearch">
            <template v-slot:brand>
                VCNexus
            </template>
        </Header>

        <router-view />
    </main>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import Header from '@/components/Header.vue';


const { t } = useI18n()


</script>

<style></style>