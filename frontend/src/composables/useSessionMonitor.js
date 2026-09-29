import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { onMounted, onUnmounted } from 'vue'


export function useSessionMonitor(interval = 60000) {
    const authStore = useAuthStore()

    let timer

    const monitor = async () => {
        if(!authStore.sessionUser){
            return
        }
        await authStore.getAuthenticatedUser()

        if(!authStore.isAuthenticated){
            await authStore.logout()
            return
        }

        if(!authStore.sessionUser){
            await authStore.logout()
        }
    }

    onMounted(()=> {
        monitor()
        timer = setInterval(monitor,interval)
    })

    onUnmounted(() => {
        clearInterval(timer)
    })
}