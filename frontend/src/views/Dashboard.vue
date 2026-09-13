<template>
    <section class="flex gap-2 grid-cols-2 mx-auto w-10/12">
        <section class="flex flex-col">
            <main class="p-6">
                <div class="mb-8">
                    <p class="text-zinc-500 text-sm">
                        Overview
                    </p>
                    <h1 class="mt-1 font-semibold text-2xl tracking-tight">
                        Dashboard
                    </h1>
                    <p class="mt-1 text-zinc-500 text-sm">
                        Here's what's happening across your organization.
                    </p>
                </div>
            </main>
            <section class="mx-auto px-6 w-5xl">
                <section class="gap-3 grid grid-cols-4">
                    <StatCard v-for="stat in stats" :key="stat.label" v-bind="stat" />
                </section>
            </section>
            <section class="mx-auto mt-6 px-6 w-5xl">
                <Fieldset legend="Links">
                    <section class="gap-3 grid grid-cols-3">
                        <LinkGroup v-for="group in linkGroups" :key="group.title" :title="group.title" :accent="group.accent" :links="group.links" />
                    </section>
                </Fieldset>
            </section>

        </section>
        <section class="mt-8">
            <Fieldset legend="Tasks">
                <TaskList :tasks="tasks"  class="flex flex-col justify-between mx-auto" />
            </Fieldset>
            <section class="flex flex-col gap-2 px-2 py-1 h-96 overflow-y-auto">
                <BulletinCard v-for="post in bulletinPosts" :key="post.title" v-bind="post" />
            </section>
        </section>
    </section>
</template>
<script setup>
import { ref } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import LinkGroup from '@/components/LinkGroup.vue'
import StatCard from '@/components/StatCard.vue'
import TaskList from '@/components/TaskList.vue'
import BulletinCard from '@/components/BulletinCard.vue'

const stats = ref([
    { label: 'Active Users', value: '5', icon: 'bi-people-fill', accent: 'emerald' },
    { label: 'Open Tasks', value: '3', icon: 'bi-list-check', accent: 'cyan' },
    { label: 'Expiring Schedule', value: '10', icon: 'bi-list-check', accent: 'orange' },
    { label: 'Tenants', value: '3', icon: 'bi-building', delta: '+50%', deltaDirection: 'up', accent: 'amber' },
])
const linkGroups = ref([
    {
        title: 'Users',
        accent: 'emerald',
        links: [
            { to: '/users/new', icon: 'bi-person-plus-fill', label: 'New' },
            { to: '/users/list', icon: 'bi-people-fill', label: 'List' },
            { to: '/users/documents', icon: 'bi-file-earmark-person', label: 'Documents' },
        ],
    },
    {
        title: 'Tenants',
        accent: 'amber',
        links: [
            { to: '/tenants/new', icon: 'bi-building-add', label: 'New' },
            { to: '/tenants/list', icon: 'bi-buildings', label: 'List' },
            { to: '/tenants/leases', icon: 'bi-file-earmark-text', label: 'Leases' },
        ],
    },
    {
        title: 'Payments',
        accent: 'rose',
        links: [
            { to: '/payments/new', icon: 'bi-cash', label: 'New Payment' },
            { to: '/payments/list', icon: 'bi-receipt-cutoff', label: 'Extract' },
            { to: '/payments/invoices', icon: 'bi-receipt', label: 'Invoices' },
        ],
    },
    {
        title: 'Tasks',
        accent: 'cyan',
        links: [
            { to: '/tasks/new', icon: 'bi-plus-square', label: 'New' },
            { to: '/tasks/list', icon: 'bi-plus-square-fill', label: 'List' },
        ],
    },
    {
        title: 'Store',
        accent: 'orange',
        links: [
            { to: '/store/products', icon: 'bi-box-seam', label: 'Products' },
            { to: '/store/inventory', icon: 'bi-clipboard-data', label: 'Inventory' },
            { to: '/store/orders', icon: 'bi-cart-check', label: 'Orders' },
            { to: '/store/suppliers', icon: 'bi-truck', label: 'Suppliers' },
        ],
    },
    {
        title: 'Schedule',
        accent: 'sky',
        links: [
            { to: '/schedule/new', icon: 'bi-calendar-plus', label: 'New' },
            { to: '/schedule/list', icon: 'bi-list-task', label: 'List' },
        ],
    },
])
const tasks = ref([
    { title: 'Setup multi-tenant system', owner: 'Development Team', due: '15/09/2026', priority: 'High', done: true },
    { title: 'Implement store management', owner: 'Development Team', due: '21/09/2026', priority: 'High', done: false },
    { title: 'Setup payment system with PIX and credit cards.', owner: 'Development Team', due: '05/10/2026', priority: 'High', done: false },
    { title: 'Whats App message handling for selling/support notifications.', owner: 'Development Team', due: '05/10/2026', priority: 'High', done: false },
])

const bulletinPosts = ref([
    {
        title: 'Adipisicing pariatur deserunt adipisicing proident.',
        body: 'Excepteur amet esse in id ipsum ea eiusmod dolore ad labore ad cillum..',
        category: 'Announcement',
        author: { name: 'Scrum Master' },
        urgent: true,
        postedAt: 'Today',
        pinned: true,
    },
    {
        title: 'Adipisicing velit officia duis ipsum est ea sit est sit.',
        body: 'Commodo ipsum nisi tempor Lorem.',
        category: 'Announcement',
        author: { name: 'Facilities Team' },
        postedAt: 'Today',
        pinned: true,
    },
    {
        title: 'Eu ipsum eiusmod tempor anim consectetur in amet tempor deserunt enim ex ipsum.',
        body: 'Enim culpa ut aliquip Lorem ullamco proident eiusmod..',
        category: 'Announcement',
        author: { name: 'Human Resources' },
        postedAt: 'Yesterday',
        pinned: true,
    },
    {
        title: 'Esse mollit irure excepteur incididunt id culpa mollit proident laborum proident consectetur aliqua consequat id.',
        body: 'Nostrud irure dolore sunt quis nisi enim dolor est..',
        category: 'Announcement',
        author: { name: 'Product Manager' },
        urgent: true,
        postedAt: '31/08/2026',
        pinned: true,
    },
    {
        title: 'Commodo cillum reprehenderit labore consectetur ipsum',
        body: 'Id incididunt cillum nisi excepteur..',
        category: 'Announcement',
        author: { name: 'CyberSecurity Team' },
        postedAt: '29/08/2026',
        pinned: true,
        urgent: true,
    },
])

</script>