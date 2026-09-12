<template>

    <main class="flex">
        <AppSidebar v-model:mobile-open="isSidebarOpen" v-model:collapsed="isSidebarCollapsed" :nav-items="Headers">
            <template #brand>{{ authStore.sessionUser?.organization_name ?? 'VCNexus' }}</template>
            <template #footer="{ collapsed }">


                <!-- Profile dropdown -->
                <Dropdown align="left" :offset="{ x: 0, y: -164 }" v-if="authStore.sessionUser">
                    <template #trigger="{ toggle }">
                        <button type="button" :class="['flex items-center gap-2 w-full rounded-md p-2']"
                            class="flex justify-center items-center hover:opacity-90 rounded-full w-9 h-9 font-medium text-zinc-100 text-sm transition-opacity" aria-label="Open user menu" @click="toggle">
                            <slot name="avatar">
                                <img v-if="authStore.sessionUser?.avatar" :src="`${storageBase}/storage/users/avatars/${authStore.sessionUser?.avatar}`" loading="lazy" class="rounded-full w-9 h-9 object-center" alt="avatar" />
                                <span v-else>
                                    {{ userInitials }}
                                </span>
                            </slot>
                            <span class="text-zinc-900 dark:text-zinc-200 text-xs truncate" v-if="!collapsed">
                                <p>{{ authStore.sessionUser.name }} {{ authStore.sessionUser.surname }} {{ authStore.sessionUser.lastname }}</p>
                                <p><b>{{ authStore.sessionUser.email }}</b></p>
                            </span>
                        </button>
                    </template>

                    <DropdownItem href="/profile">
                        <template v-slot:icon>
                            <i class="bi bi-person-fill"></i>
                        </template>
                        {{ t('header.dropdown.profile') }}
                    </DropdownItem>
                    <DropdownItem href="/settings">
                        <template v-slot:icon> <i class="bi bi-gear-fill"></i></template>
                        {{ t('header.dropdown.settings') }}
                    </DropdownItem>
                    <DropdownItem deactivated="true" @click="handleLogout">
                        <template v-slot:icon> <i class="bi-box-arrow-left"></i></template>
                        {{ t('header.dropdown.logout') }}
                    </DropdownItem>
                </Dropdown>


                <!-- Profile dropdown -->
                <Dropdown align="left" v-if="!authStore.sessionUser">
                    <template #trigger="{ toggle }">
                        <button type="button" class="flex justify-center items-center bg-emerald-500 hover:opacity-90 rounded-full w-9 h-9 font-medium text-zinc-100 text-sm transition-opacity" aria-label="Open user menu" @click="toggle">
                            <slot name="avatar">{{ "U" }}</slot>
                        </button>
                    </template>
                    <DropdownItem :redirectTo=loginPage>
                        <template v-slot:icon> <i class="bi bi-door-open-fill"></i></template>
                        Login
                    </DropdownItem>
                    <DropdownItem :redirectTo=registerPage>
                        <template v-slot:icon> <i class="bi bi-person-plus-fill"></i></template>
                        Register
                    </DropdownItem>
                </Dropdown>
            </template>
        </AppSidebar>

        <div class="flex flex-col flex-1 min-w-0">

            <Header @search="onSearch">
                <template v-slot:brand>
                    {{ authStore.sessionUser?.organization_name ?? 'VCNexus' }}
                </template>
            </Header>

            <main class="flex-1 p-6">
                <router-view />
            </main>
        </div>
    </main>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter, useRoute } from 'vue-router'
import Header from '@/components/Header.vue';
import { useAuthStore } from '@/stores/authStore';
import AppSidebar from '@/components/AppSidebar.vue'

import Dropdown from '@/components/Dropdown.vue'
import DropdownItem from '@/components/DropdownItem.vue'

const { t } = useI18n()

const isSidebarOpen = ref(false)
const isSidebarCollapsed = ref(false)
const authStore = useAuthStore();
const storageBase = import.meta.env.VITE_API_URL
const router = useRouter()

const Headers = [
    { label: t('header.links.home'), href: '/', icon: 'bi bi-house-fill' },
]

// ======== Users
const usersOptionsUrls = {
    'users.view': { label: t('header.links.users.list'), href: '/users/list', icon: 'bi bi-people-fill' },
    'users.new': { label: t('header.links.users.new'), href: '/users/new/', icon: 'bi bi-person-plus-fill' },
    'users.documents': { label: t('header.links.users.documents'), href: '/users/documents/', icon: 'bi bi-file-earmark' },
    'users.reports': { label: t('header.links.users.reports'), href: '/users/reports/', icon: 'bi bi-file-spreadsheet' },
};

const userOptions = { label: t('header.links.users.users'), children: [], icon: 'bi bi-people' };

userOptions.children = Object.entries(usersOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item);


// ======== Tenants
const tenantsOptionsUrls = {
    'tenants.view': { label: t('header.links.tenants.list'), href: '/tenants/list', icon: 'bi bi-building-fill' },
    'tenants.new': { label: t('header.links.tenants.new'), href: '/tenants/new/', icon: 'bi bi-building-add' },
    'tenants.documents': { label: t('header.links.tenants.documents'), href: '/tenants/documents/', icon: 'bi bi-file-earmark' },
    'tenants.reports': { label: t('header.links.tenants.reports'), href: '/tenants/reports/', icon: 'bi bi-file-spreadsheet' },
}

const tenantOptions = { label: t('header.links.tenants.tenants'), children: [], icon: 'bi bi-building' };

tenantOptions.children = Object.entries(tenantsOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item)

// ======== Tasks
const tasksOptions = { label: t('header.links.tasks.tasks'), children: [], icon: 'bi bi-clipboard-check' };

const tasksOptionsUrls = {
    'tasks.new': { label: t('header.links.tasks.new'), href: '/tasks/new/', icon: 'bi bi-plus-square-fill' },
    'tasks.view': { label: t('header.links.tasks.list'), href: '/tasks/list/', icon: 'bi bi-list-task' }
}

tasksOptions.children = Object.entries(tasksOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item);


const schedulesOptions = { label: t('header.links.schedule.schedule'), children: [], icon: 'bi bi-calendar' };

const schedulesOptionsUrls = {
    'schedules.calendar': { label: t('header.links.schedule.calendar'), href: '/schedule/calendar/', icon: 'bi bi-calendar' },
    'schedules.new': { label: t('header.links.schedule.new'), href: '/schedule/new/', icon: 'bi bi-calendar-plus' },
};
schedulesOptions.children = Object.entries(schedulesOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item)



Headers.push(userOptions)
Headers.push(tenantOptions)
Headers.push(tasksOptions)
Headers.push(schedulesOptions)


const userInitials = computed(() =>
  (authStore.sessionUser.name)
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
  +
  (authStore.sessionUser.lastname)
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)

async function handleLogout() {
  const ok = await authStore.logout()
  if (ok) {
    router.push({ name: 'login' })
  }
}
</script>

<style></style>