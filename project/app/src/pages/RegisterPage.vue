<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="flex flex-center bg-grey-2">
        <q-card class="register-card" style="min-width: 330px; width: calc(100% - 32px); max-width: 450px;">
      <q-card-section class="text-center q-pb-none">
        <div class="text-h4 text-primary q-mb-md">CRM Homland</div>
        <div class="text-h6 text-grey-7">Create your account</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="form.name"
            label="Full Name"
            outlined
            dense
            :error="!!errors.name"
            :error-message="errors.name"
            :loading="loading"
            required
          >
            <template v-slot:prepend>
              <q-icon name="person" />
            </template>
          </q-input>

          <q-input
            v-model="form.email"
            label="Email"
            type="email"
            outlined
            dense
            :error="!!errors.email"
            :error-message="errors.email"
            :loading="loading"
            required
          >
            <template v-slot:prepend>
              <q-icon name="email" />
            </template>
          </q-input>

          <q-input
            v-model="form.mobile"
            label="Mobile (Optional)"
            outlined
            dense
            :error="!!errors.mobile"
            :error-message="errors.mobile"
            :loading="loading"
            mask="### #### #####"
          >
            <template v-slot:prepend>
              <q-icon name="phone" />
            </template>
          </q-input>

          <q-input
            v-model="form.password"
            label="Password"
            type="password"
            outlined
            dense
            :error="!!errors.password"
            :error-message="errors.password"
            :loading="loading"
            required
          >
            <template v-slot:prepend>
              <q-icon name="lock" />
            </template>
          </q-input>

          <q-input
            v-model="form.password_confirmation"
            label="Confirm Password"
            type="password"
            outlined
            dense
            :error="!!errors.password_confirmation"
            :error-message="errors.password_confirmation"
            :loading="loading"
            required
          >
            <template v-slot:prepend>
              <q-icon name="lock" />
            </template>
          </q-input>

          <q-input
            v-model="form.address"
            label="Address (Optional)"
            outlined
            dense
            type="textarea"
            rows="2"
            :loading="loading"
          >
            <template v-slot:prepend>
              <q-icon name="location_on" />
            </template>
          </q-input>

          <div>
            <q-btn
              type="submit"
              label="Register"
              color="primary"
              class="full-width"
              :loading="loading"
              unelevated
            />
          </div>

          <div class="text-center">
            <span class="text-grey-7">Already have an account? </span>
            <q-btn
              flat
              dense
              no-caps
              label="Login"
              color="primary"
              @click="$router.push('/login')"
              class="q-pa-none"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { useAuthStore } from 'stores/auth'

export default defineComponent({
  name: 'RegisterPage',
  setup() {
    const router = useRouter()
    const $q = useQuasar()
    const authStore = useAuthStore()
    
    // Helper function to show notifications
    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const form = ref({
      name: '',
      email: '',
      mobile: '',
      password: '',
      password_confirmation: '',
      address: '',
    })

    const errors = ref({})
    const loading = ref(false)

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const result = await authStore.register(form.value)

        if (result.success) {
          showNotify({
            type: 'positive',
            message: result.message || 'Registration successful! Please wait for admin approval.',
            position: 'top',
            timeout: 5000,
          })
          router.push('/login')
        } else {
          if (result.errors) {
            errors.value = result.errors
          }
          showNotify({
            type: 'negative',
            message: result.message || 'Registration failed',
            position: 'top',
          })
        }
      } catch {
        showNotify({
          type: 'negative',
          message: 'An error occurred. Please try again.',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      errors,
      loading,
      onSubmit,
    }
  },
})
</script>

<style scoped>
.register-card {
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>

