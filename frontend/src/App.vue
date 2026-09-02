<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'


const authStore = useAuthStore()

const user = computed(() => authStore.sessionUser)

onMounted(async () => {
    await authStore.fetchUser()
})


// change the layout based on the current user
const layout = computed(() => {
    return user.value ? 'Default' : 'Guest'
})

</script>

<template>
    <component :is="layout">
        <router-view />
    </component>
</template>
