<template>
    <nav class="bg-white dark:bg-stone-800 px-4 lg:px-6 py-2.5 border-stone-200">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
            <div class="flex items-center gap-2">
                <span class="self-center font-semibold dark:text-white text-xl whitespace-nowrap">
                    <RouterLink to="/">
                        Home
                    </RouterLink>
                </span>
                <span class="self-center font-semibold dark:text-white text-xl whitespace-nowrap">
                    <RouterLink to="/about">
                        About
                    </RouterLink>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <ul class="flex items-center gap-2">
                    <li class="relative hover:bg-stone-100 dark:hover:bg-stone-700 no-underline">
                        <!-- WebChat -->
                        <RouterLink :to="{ name: 'web.chat' }" class="flex items-center gap-2 hover:bg-stone-100 dark:hover:bg-stone-700 p-2 rounded-lg font-medium text-stone-900 dark:text-stone-100 text-sm">
                            <i class="bi-chat-left-text-fill bi"></i>
                            WebChat
                        </RouterLink>
                    </li>
                </ul>
            </div>

            <div class="flex items-end ms-auto me-0">
                <template v-if="user">
                    <Dropdown :authStore="authStore">
                        <template #trigger="{ toggle }">
                            <DropdownButton @click="toggle">

                                <template #avatar>
                                    <template v-if="user.avatar">
                                        <img class="rounded-full w-8 h-8" :src="user.avatar" :alt="user.name">
                                    </template>
                                    <template v-else>
                                        <span class="bg-stone-500 dark:bg-stone-600 mr-2 px-2.5 py-0.5 rounded font-medium text-stone-100 dark:text-stone-400 text-sm">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                    </template>
                                </template>

                                <template #name>
                                    <template v-if="user.name">
                                        {{ user.name }}
                                    </template>
                                    <template v-else>
                                        User
                                    </template>
                                </template>
                            </DropdownButton>
                        </template>
                        <template #content>
                            <DropdownItem @click="router.push({ name: 'home' })" :deactivated="false" label="Profile" href="#">
                                <template #icon> <i class="bi-person-fill bi"></i> </template>
                                <template #label> Profile </template>
                            </DropdownItem>
                            <DropdownItem @click="logout" :deactivated="false" label="Logout" href="#">
                                <template #icon> <i class="bi-box-arrow-right bi"></i> </template>
                                <template #label> Logout </template>
                            </DropdownItem>
                        </template>
                    </Dropdown>
                </template>
                <template v-else>
                    <RouterLink :to="{ name: 'login' }" class="hover:bg-stone-100 dark:hover:bg-stone-700 p-2 rounded-lg font-medium text-stone-900 dark:text-stone-100 text-sm">
                        Login
                    </RouterLink>
                </template>
                <DarkmodeButton />

            </div>
        </div>
    </nav>
</template>

<script setup>
import DarkmodeButton from '@/components/DarkmodeButton.vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authentication/authenticationStore'
import Dropdown from './Dropdown.vue'
import DropdownButton from './DropdownButton.vue'
import DropdownItem from './DropdownItem.vue'

const router = useRouter()
const authStore = useAuthStore()
const user = authStore.sessionUser

async function logout() {
    const ok = await authStore.logout()
    if (!ok) return
    router.push({ name: 'login' })
}

</script>