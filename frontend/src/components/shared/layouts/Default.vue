<template>

    <main class="bg-neutral-50 dark:bg-neutral-900 min-h-screen">
        <Header :nav-items="[
            { label: 'Home', href: '/', icon: 'bi bi-house-fill' },
            {
                label: 'Schedule', children: [
                    { label: 'Calendar', href: '/schedule/new/', icon: 'bi bi-calendar-plus' },
                    { label: 'List', href: '/schedule/list/', icon: 'bi bi-list-task' },
                ] , icon:'bi bi-calendar',                
            },
            {
                label: 'Tasks', children: [
                    { label: 'List', href: '/tasks/list/', icon: 'bi bi-list-task'  },
                    { label: 'New', href: '/tasks/new/', icon: 'bi bi-plus-square-fill'  },
                ], icon: 'bi bi-list-task'
            },
            {
                label: 'Users', children: [
                    { label: 'New', href: '/users/new/', icon: 'bi bi-person-plus-fill' },
                    { label: 'List', href: '/users/list/', icon: 'bi bi-list-task' },
                    { label: 'Documents', href: '/users/documents', icon: 'bi bi-file-earmark' },
                    { label: 'Reports', href: '/users/reports/', icon: 'bi bi-file-spreadsheet' },
                ], icon: 'bi bi-people-fill'
            },
            {
                label: 'Entity',children: [
                    { label: 'New', href: '/entities/new/', icon: 'bi bi-building-add' },
                    { label: 'List', href: '/entities/list/', icon: 'bi bi-buildings' },
                    { label: 'Reports', href: 'entities/report/', icon: 'bi bi-building-fill-exclamation' },
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
import Header from '@/components/Header/Header.vue';


</script>

<style></style>