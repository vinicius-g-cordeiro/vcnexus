import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'



export function useSessionMonitor(interval = 60000) {
    const authStore = useAuthStore()
    let timer
    const router = useRouter()
    const monitor = async () => {
        const isAuthenticated = await authStore.monitorUser()
        console.log(`is authenticated: ${isAuthenticated}`)
        if(!isAuthenticated){
            router.push({ name: 'login' })
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