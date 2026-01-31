<template>
  <q-page padding>
    <div class="text-h4 q-mb-md">Dashboard</div>
    <div class="text-body1 text-grey-7">Welcome to CRM Homland</div>
    
    <div class="q-mt-lg">
      <q-card>
        <q-card-section>
          <div class="text-h6">User Information</div>
          <div v-if="authStore.user" class="q-mt-md">
            <p><strong>Name:</strong> {{ authStore.user.name }}</p>
            <p><strong>Email:</strong> {{ authStore.user.email }}</p>
            <p><strong>Role:</strong> {{ authStore.user.role }}</p>
          </div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue'
import { useAuthStore } from 'stores/auth'

export default defineComponent({
  name: 'DashboardPage',
  setup() {
    const authStore = useAuthStore()
    return {
      authStore,
    }
  },
})
</script>

