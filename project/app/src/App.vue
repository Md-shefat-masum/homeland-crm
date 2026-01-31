<template>
  <router-view />
</template>

<script>
import { defineComponent, onMounted } from 'vue'
import { useAuthStore } from 'stores/auth'

export default defineComponent({
  name: 'App',
  setup() {
    const authStore = useAuthStore()

    onMounted(() => {
      // Initialize auth from localStorage on app start
      if (process.env.CLIENT) {
        authStore.initAuth()
      }
    })
  },
})
</script>
