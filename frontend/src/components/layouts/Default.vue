<template>
    <section class="flex">
        <AppSidebar v-model:mobile-open="isSidebarOpen" v-model:collapsed="isSidebarCollapsed" :nav-items="Headers">
            <template #brand>{{ authStore.sessionUser?.organization_name ?? 'VCNexus' }}</template>
            <template #footer="{ collapsed }">
                <Dropdown align="left" :offset="{ x: 0, y: -164 }" v-if="authStore.sessionUser">
                    <template #trigger="{ toggle }">
                        <button type="button" :class="['flex items-center gap-2 w-full rounded-md p-2']" class="flex justify-center items-center hover:opacity-90 rounded-full w-9 h-9 font-medium text-zinc-100 text-sm transition-opacity cursor-pointer" aria-label="Open user menu" @click="toggle">
                            <slot name="avatar">
                                <img v-if="authStore.sessionUser?.avatar" :src="`${storageBase}/storage/users/avatars/${authStore.sessionUser?.avatar}`" loading="lazy" class="rounded-full w-9 h-9 object-center" alt="avatar" />
                                <span v-else>
                                    {{ userInitials }}
                                </span>
                            </slot>
                            <span class="text-[10px] text-zinc-900 dark:text-zinc-200" v-if="!collapsed">
                                <p class="truncate">{{ authStore.sessionUser.name }} {{ authStore.sessionUser.surname }} {{ authStore.sessionUser.lastname }}</p>
                                <p class="truncate"><b>{{ authStore.sessionUser.email }}</b></p>
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
        <div class="flex flex-col flex-1 min-w-0 text-xs">
            <Header @search="onSearch">
                <template v-slot:brand>
                    {{ authStore.sessionUser?.organization_name ?? 'VCNexus' }}
                </template>
            </Header>
            <article class="flex-1 p-6">
                <router-view />
            </article>
        </div>
    </section>
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


const tasksOptions = { label: t('header.links.tasks.tasks'), children: [], icon: 'bi bi-clipboard-check' };
const tasksOptionsUrls = {
    'tasks.view': { label: t('header.links.tasks.list'), href: '/tasks/list/', icon: 'bi bi-list-task' },
    'tasks.new': { label: t('header.links.tasks.new'), href: '/tasks/new/', icon: 'bi bi-plus-square-fill' },
}
tasksOptions.children = Object.entries(tasksOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item);


const schedulesOptions = { label: t('header.links.schedule.schedule'), children: [], icon: 'bi bi-calendar-week' };
const schedulesOptionsUrls = {
    'schedules.view': { label: t('header.links.schedule.list'), href: '/schedule/list/', icon: 'bi bi-calendar-range' },
    'schedules.calendar': { label: t('header.links.schedule.calendar'), href: '/schedule/calendar/', icon: 'bi bi-calendar-week' },
    'schedules.new': { label: t('header.links.schedule.new'), href: '/schedule/new/', icon: 'bi bi-calendar-plus' },
};
schedulesOptions.children = Object.entries(schedulesOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item)



const paymentsOptions = { label: t('header.links.payments.payments'), children: [], icon: 'bi bi-cash-coin' };
const paymentsOptionsUrls = {
    'payments.view': { label: t('header.links.payments.list'), href: '/payments/list/', icon: 'bi bi-receipt-cutoff' },
    'payments.new': { label: t('header.links.payments.new'), href: '/payments/new/', icon: 'bi bi-cash' },
    'payments.wallet': { label: t('header.links.payments.wallet'), href: '/payments/wallet/', icon: 'bi bi-wallet' },
};
paymentsOptions.children = Object.entries(paymentsOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item)


const storeOptions = { label: t('header.links.store.store'), children: [], icon: 'bi bi-shop' };
const storeOptionsUrls = {
    'store.view': { label: t('header.links.store.list'), href: '/store/list/', icon: 'bi bi-clipboard-data' },
    'store.orders': { label: t('header.links.store.orders'), href: '/store/orders/', icon: 'bi bi-cart4' },
    'store.suppliers': { label: t('header.links.store.suppliers'), href: '/store/suppliers/', icon: 'bi bi-truck-flatbed' },
    'store.deliveries': { label: t('header.links.store.deliveries'), href: '/store/deliveries/', icon: 'bi bi-truck' },
    'store.inventory': { label: t('header.links.store.inventory'), href: '/store/inventory/', icon: 'bi bi-upc' },
};
storeOptions.children = Object.entries(storeOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item)


const productsOptions = { label: t('header.links.products.products'), children: [], icon: 'bi bi-box-seam' };
const productsOptionsUrls = {
    'products.view': { label: t('header.links.products.list'), href: '/products/list/', icon: 'bi bi-list-stars' },
    'products.new': { label: t('header.links.products.new'), href: '/products/new/', icon: 'bi bi-box2' },
    'products.stock': { label: t('header.links.products.stock'), href: '/products/stock/', icon: 'bi bi-boxes' },
};
productsOptions.children = Object.entries(productsOptionsUrls)
    .filter((permission) => authStore.hasPermission(permission))
    .map(([, item]) => item)

Headers.push(userOptions)
Headers.push(tenantOptions)
Headers.push(tasksOptions)
Headers.push(schedulesOptions)
Headers.push(storeOptions)
Headers.push(paymentsOptions)
Headers.push(productsOptions)

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