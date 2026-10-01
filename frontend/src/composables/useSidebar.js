import { ref, watch } from 'vue'

const stored = localStorage.getItem('isSidebarOpen')
const isOpen = ref(stored === null ? true : stored === 'true')

watch(isOpen, (value) => {
    localStorage.setItem('isSidebarOpen', String(value))
})

export function useSidebar() {
    function toggle() {
        isOpen.value = !isOpen.value
    }

    return { isOpen, toggle }
}