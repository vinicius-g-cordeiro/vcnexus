<template>
    <nav class="top-0 z-40 sticky bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-zinc-200 dark:border-zinc-800 border-b">
        <div class="flex items-center gap-3 mx-auto px-4 lg:px-6 max-w-screen-xl h-14">
            <RouterLink to="/" class="flex items-center hover:opacity-80 rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-olive-wood-500 transition shrink-0">
                <img class="w-8 h-8" src="@/assets/logo.png" alt="logo">
            </RouterLink>

            <form class="hidden sm:block relative flex-1 mx-auto max-w-md" role="search" @submit.prevent="onSearch">
                <i class="top-1/2 left-3 absolute text-zinc-400 text-sm -translate-y-1/2 pointer-events-none bi bi-search"></i>
                <input
                    ref="searchInput"
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search..."
                    aria-label="Search"
                    class="bg-zinc-100 dark:bg-zinc-800 py-2 pr-10 pl-9 border border-transparent focus:border-olive-wood-500 rounded-lg outline-none focus:ring-2 focus:ring-olive-wood-500/30 w-full text-zinc-900 dark:text-zinc-100 text-sm transition placeholder-zinc-400"
                />
                <kbd class="top-1/2 right-2.5 absolute bg-white dark:bg-zinc-700 px-1.5 py-0.5 border border-zinc-200 dark:border-zinc-600 rounded text-[0.65rem] text-zinc-500 dark:text-zinc-300 -translate-y-1/2 pointer-events-none">/</kbd>
            </form>

            <div class="flex items-center gap-1 ms-auto me-0">
                <button type="button" class="sm:hidden flex justify-center items-center hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg w-9 h-9 text-zinc-600 dark:text-zinc-300 transition" aria-label="Search" @click="searchInput?.focus()">
                    <i class="bi bi-search"></i>
                </button>

                <template v-if="user">
                    <button type="button" class="relative flex justify-center items-center hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg w-9 h-9 text-zinc-600 dark:text-zinc-300 transition" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                        <span class="top-2 right-2 absolute bg-olive-wood-500 rounded-full ring-2 ring-white dark:ring-zinc-900 w-2 h-2"></span>
                    </button>
                </template>

                <DarkmodeButton />

                <div class="bg-zinc-200 dark:bg-zinc-700 mx-1 w-px h-6"></div>

                <template v-if="user">
                    <Dropdown :authStore="authStore">
                        <template #trigger="{ toggle }">
                            <DropdownButton @click="toggle">

                                <template #avatar>
                                    <template v-if="user.avatar">
                                        <img class="rounded-full ring-2 ring-zinc-200 dark:ring-zinc-700 w-8 h-8 object-cover" :src="user.avatar" :alt="user.name">
                                    </template>
                                    <template v-else>
                                        <span class="flex justify-center items-center bg-olive-wood-500/15 rounded-full w-8 h-8 text-olive-wood-500 text-sm">
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

                        <template #header>
                            <div class="px-4 py-3 border-zinc-200 dark:border-zinc-700 border-b">
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm truncate">{{ user.name || 'User' }}</p>
                                <p v-if="user.email" class="text-zinc-500 dark:text-zinc-400 text-xs truncate">{{ user.email }}</p>
                            </div>
                        </template>

                        <template #content>
                            <DropdownItem @click="router.push({ name: 'users.edit', params: { uuid: user.uuid } })"  :deactivated="false" label="Profile" :to="{ name: 'users.edit', params: { uuid: user.uuid } }">
                                <template #icon> <i class="bi-person bi"></i> </template>
                                <template #label> Profile </template>
                            </DropdownItem>
                            <DropdownItem :deactivated="false" label="Settings" href="#">
                                <template #icon> <i class="bi-gear bi"></i> </template>
                                <template #label> Settings </template>
                            </DropdownItem>
                            <DropdownItem :deactivated="false" label="Help" href="#">
                                <template #icon> <i class="bi-question-circle bi"></i> </template>
                                <template #label> Help </template>
                            </DropdownItem>
                            <div class="my-1 border-zinc-200 dark:border-zinc-700 border-t"></div>
                            <DropdownItem @click="logout" :deactivated="false" danger label="Logout" href="#">
                                <template #icon> <i class="bi-box-arrow-right bi"></i> </template>
                                <template #label> Logout </template>
                            </DropdownItem>
                        </template>
                    </Dropdown>
                </template>
                <template v-else>
                    <RouterLink :to="{ name: 'login' }" class="bg-olive-wood-500 hover:bg-olive-wood-600 px-4 py-1.5 rounded-lg font-medium text-white text-sm transition">
                        Login
                    </RouterLink>
                </template>
            </div>
        </div>
    </nav>
</template>
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import DarkmodeButton from '@/components/DarkmodeButton.vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authentication/authenticationStore'
import Dropdown from './Dropdown.vue'
import DropdownButton from './DropdownButton.vue'
import DropdownItem from './DropdownItem.vue'

const emit = defineEmits(['search'])

const router = useRouter()
const authStore = useAuthStore()
const user = authStore.sessionUser

const searchQuery = ref('')
const searchInput = ref(null)

function onSearch() {
    emit('search', searchQuery.value.trim())
}

function focusSearch(e) {
    const tag = e.target?.tagName
    if (e.key !== '/' || tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || e.target?.isContentEditable) return
    e.preventDefault()
    searchInput.value?.focus()
}

onMounted(() => window.addEventListener('keydown', focusSearch))
onBeforeUnmount(() => window.removeEventListener('keydown', focusSearch))

async function logout() {
    const ok = await authStore.logout()
    if (!ok) {
        // check if we got a 401 error from the backend, meaning it logged out the user from the session, so this would techically be a successful logout
        if(authStore.error?.response?.status === 401){
            router.push({ name: 'login' })
        }
        return
    }
    router.push({ name: 'login' })
}

</script>