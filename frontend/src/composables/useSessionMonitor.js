import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'



export function useSessionMonitor(interval = 60000) {
    const authStore = useAuthStore()
    let timer
    let initialTime = Date.now()
    const router = useRouter()
    const monitor = async () => {
        console.log(`Checking session.... ${Date.now()}, Elapsed time: ${Date.now() - initialTime}`)
        await authStore.getAuthenticatedUser()
        
        if(!authStore.sessionUser){            
            router.replace({ name: 'login' })
        }

        if(!authStore.isAuthenticated){
            router.replace({ name: 'login' })
        }

        if(!authStore.sessionUser){
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