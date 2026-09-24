<template>
  <component :is="layout">
    <router-view>

    </router-view>
  </component>
</template>

<script setup="js">
import { useI18n } from 'vue-i18n'
import { ref, computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/authentication/authenticationStore'

const { t } = useI18n()
// send the intl to all the components
window.intl = t

const authStore = useAuthStore()

const { sessionUser } = storeToRefs(authStore)
const layout = computed(() => {
  if (sessionUser.value) {
    return 'Guest'
  }
  return 'Default'
})
</script>
