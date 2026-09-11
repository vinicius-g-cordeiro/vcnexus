<template>

    <main class="bg-zinc-50 dark:bg-zinc-900 min-h-screen">
        <Header :nav-items="isSuperAdmin ? superAdminHeaders : AdminHeaders" user-name="" @search="onSearch">
            <template v-slot:brand>
                {{  authStore.sessionUser?.organization_name ?? 'VCNexus' }}
            </template>
        </Header>

        <router-view />
    </main>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import Header from '@/components/Header.vue';
import { useAuthStore } from '@/stores/authStore';

const { t } = useI18n()

const authStore = useAuthStore();
const isSuperAdmin = authStore.isSuperAdmin
const canManageAccess = authStore.canManageAccess

const headerOptions = ref([]);


const superAdminHeaders = [
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
        label: t('header.links.tenants.tenants'),children: [
            { label:  t('header.links.tenants.new'), href: '/tenants/new/', icon: 'bi bi-building-add' },
            { label: t('header.links.tenants.list'), href: '/tenants/list/', icon: 'bi bi-buildings' },
            { label: t('header.links.tenants.reports'), href: 'tenants/report/', icon: 'bi bi-building-fill-exclamation' },
        ], icon: 'bi bi-building-fill'
    },
]

const accessManagement =  {
    label: t('header.links.users.users'), children: [
        { label:  t('header.links.users.new'), href: '/users/new/', icon: 'bi bi-person-plus-fill' },
        { label: t('header.links.users.list'), href: '/users/list/', icon: 'bi bi-list-task' },
        { label: t('header.links.users.documents'), href: '/users/documents', icon: 'bi bi-file-earmark' },
        { label: t('header.links.users.reports'), href: '/users/reports/', icon: 'bi bi-file-spreadsheet' },
    ], icon: 'bi bi-people-fill'
}

const AdminHeaders = [
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
]


// users.view
// users.delete
// users.invite
// users.edit
// billing.view
// billing.manage
// reports.export
// reports.view

const usersOptionsUrls = {
    'users.view': { label: t('header.links.users.list'), href: '/users/list', icon: 'bi bi-people-fill' },
    'users.new': { label: t('header.links.users.new'), href: '/users/new/', icon: 'bi bi-person-plus-fill' },
    'users.documents': { label: t('header.links.users.documents'), href: '/users/documents/', icon: 'bi bi-file-earmark' },
    'users.reports': { label: t('header.links.users.reports'), href: '/users/reports/', icon: 'bi bi-file-spreadsheet' },
};

const userOptions = { label: t('header.links.users.users'), children: [] };

userOptions.children = Object.entries(usersOptionsUrls)
    .filter(([permission]) => authStore?.userPermissions?.includes(permission))
    .map(([, item]) => item);


AdminHeaders.push(userOptions)

</script>

<style></style>