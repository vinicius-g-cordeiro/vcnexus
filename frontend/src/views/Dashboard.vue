<template>
    <main class="gap-2 grid grid-cols-2 mx-auto w-10/12">
        <section>
            <!-- Content -->
            <main class="p-6">
                <!-- Page heading -->
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

            <section class="mx-auto mt-6 px-6 w-5xl">
                <Fieldset legend="Bulletin Board">
                    <section class="gap-3 grid grid-cols-3">
                        <BulletinCard v-for="post in bulletinPosts" :key="post.title" v-bind="post" />
                    </section>
                </Fieldset>
            </section>
        </section>

        <section class="ms-auto mt-6 px-6">
            <Fieldset legend="System Activity">
                <ActivityFeed :events="activityEvents" />
            </Fieldset>

            <Fieldset legend="Tasks">
                <TaskList :tasks="tasks" />
            </Fieldset>
        </section>

    </main>
</template>

<script setup>
import { ref } from 'vue'
import BulletinCard from '@/components/BulletinCard.vue'
import Fieldset from '@/components/Fieldset.vue'
import LinkGroup from '@/components/LinkGroup.vue'
import StatCard from '@/components/StatCard.vue'
import TaskList from '@/components/TaskList.vue'
import ActivityFeed from '@/components/ActivityFeed.vue'

// Placeholder metrics — wire up to real endpoints when available.
const stats = ref([
    { label: 'Active Users', value: '5', icon: 'bi-people-fill', delta: '+4.2%', deltaDirection: 'up', accent: 'emerald' },
    { label: 'Open Tasks', value: '10', icon: 'bi-list-check', delta: '-8.1%', deltaDirection: 'down', accent: 'cyan' },
    { label: 'Revenue (MTD)', value: '$100', icon: 'bi-cash-stack', delta: '+12.6%', deltaDirection: 'up', accent: 'rose' },
    { label: 'Active Tenants', value: '5', icon: 'bi-buildings', delta: '+1.3%', deltaDirection: 'up', accent: 'amber' },
])

// Each group carries a small accent color to make modules scannable at a
// glance without relying on icons alone. Kept subtle (border + icon tint),
// not full colored card backgrounds — this is a dense admin surface, not a
// marketing page.
const linkGroups = ref([
    {
        title: 'Schedule',
        accent: 'sky',
        links: [
            { to: '/schedule/new', icon: 'bi-calendar-plus', label: 'New' },
            { to: '/schedule/list', icon: 'bi-list-task', label: 'List' },
        ],
    },
    {
        title: 'Events',
        accent: 'violet',
        links: [
            { to: '/events/new', icon: 'bi-calendar-event', label: 'New' },
            { to: '/events/list', icon: 'bi-list-task', label: 'List' },
            { to: '/events/calendar', icon: 'bi-calendar3', label: 'Calendar' },
        ],
    },
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
        title: 'Entities',
        accent: 'indigo',
        links: [
            { to: '/entities/new', icon: 'bi-diagram-3', label: 'New' },
            { to: '/entities/list', icon: 'bi-diagram-3-fill', label: 'List' },
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
])

const tasks = ref([
    { title: 'Review Q3 tenant lease renewals', owner: 'Facilities Team', due: 'Today', priority: 'High', done: false },
    { title: 'Approve pending payment extract', owner: 'Finance', due: 'Today', priority: 'High', done: false },
    { title: 'Onboard new store supplier', owner: 'Store Management', due: 'Tomorrow', priority: 'Medium', done: false },
    { title: 'Update employee documents', owner: 'Human Resources', due: 'Sep 5', priority: 'Medium', done: false },
    { title: 'Publish September schedule', owner: 'Scrum Master', due: 'Sep 6', priority: 'Low', done: true },
    { title: 'Archive closed entities', owner: 'Ops', due: 'Sep 8', priority: 'Low', done: false },
])

const activityEvents = ref([
    { actor: 'System', title: 'completed nightly payments reconciliation', time: '5 minutes ago', type: 'success' },
    { actor: 'Maria Santos', title: 'created a new tenant record for Unit 402', time: '32 minutes ago', type: 'info' },
    { actor: 'CyberSecurity Team', title: 'flagged 3 failed login attempts', time: '1 hour ago', type: 'warning' },
    { actor: 'System', title: 'failed to sync inventory with supplier feed', time: '2 hours ago', type: 'error' },
    { actor: 'Jonas Firmino', title: 'approved a new payment extract', time: '3 hours ago', type: 'success' },
    { actor: 'System', title: 'ran scheduled backup', time: '6 hours ago', type: 'info' },

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
