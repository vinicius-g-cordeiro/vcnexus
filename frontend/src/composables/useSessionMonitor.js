import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'



export function useSessionMonitor(interval = 60000) {
    const authStore = useAuthStore()

    let timer

    const router = useRouter()
    const monitor = async () => {
        await authStore.getAuthenticatedUser()
        
        if(!authStore.sessionUser){
            return
        }

        if(!authStore.isAuthenticated){
            await authStore.logout()
            router.replace({ name: 'login' })
        }

        if(!authStore.sessionUser){
            await authStore.logout()
            router.replace({ name: 'login' })
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