import {onMounted, onBeforeUnmount} from 'vue'

export default function useClickOutside(ref, callback) {
    onMounted(() => {
        document.addEventListener('click', handleClick, true)
    })
    onBeforeUnmount(() => {
        document.removeEventListener('click', handleClick, true)
    })
    function handleClick(e) {
        if (ref.value && !ref.value.contains(e.target)) {
            callback()
        }
    }
}

