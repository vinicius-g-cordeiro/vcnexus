<template>
<div class="flex md:flex-row flex-col gap-4 bg-zinc-100 dark:bg-zinc-950 min-h-screen">
        <aside class="flex-shrink-0 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 md:border-r w-full md:w-64">
            <div class="p-4 border-zinc-200 dark:border-zinc-800 border-b">
                <h1 class="font-semibold text-zinc-900 dark:text-zinc-100 text-base">Help</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs">Select what information you need help with.</p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <button v-for="section in sections" :key="section.name" type="button" @click="selectedSection = section.name" :class="[
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-left transition-colors',
                    selectedSection === section.name
                        ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900'
                        : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'
                ]">
                    <i :class="['bi', section.icon, 'text-base']"></i>
                    {{ section.label }}
                    <i v-if="section.required && !sectionComplete[section.name]" class="ms-auto text-amber-500 text-xl bi bi-dot"></i>
                </button>
            </nav>
        </aside>
</div>
</template>

<script setup lang="js">
import { ref } from 'vue'

const sections = [
    { name: 'profile', label: 'Profile', icon: 'bi-person' },
    { name: 'tenants', label: 'Tenants/Organizations', icon: 'bi-buildings-fill' },
    { name: 'tasks', label: 'Tasks', icon: 'bi-list' }
]

const selectedSection = ref(null)


</script>
