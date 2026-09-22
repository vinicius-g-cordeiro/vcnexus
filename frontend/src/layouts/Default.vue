<template>    
    <router-view />
</template>

<script setup lang="js">
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
onMounted(() => {
    // ...
})
</script>
