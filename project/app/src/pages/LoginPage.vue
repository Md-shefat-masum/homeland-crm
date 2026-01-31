<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="flex flex-center bg-grey-2">
        <q-card class="login-card" style="min-width: 330px; width: calc(100% - 32px); max-width: 400px;">
      <q-card-section class="text-center q-pb-none">
        <div class="text-h4 text-primary q-mb-md">CRM Homland</div>
        <div class="text-h6 text-grey-7">Login to your account</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="form.login"
            label="Email or Mobile"
            outlined
            dense
            :error="!!errors.login"
            :error-message="errors.login"
            :loading="loading"
          >
            <template v-slot:prepend>
              <q-icon name="person" />
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
          >
            <template v-slot:prepend>
              <q-icon name="lock" />
            </template>
          </q-input>

          <div style="display:none;">
            <q-input
              v-model="form.device_id"
              label="Device ID"
              outlined
              dense
              :error="!!errors.device_id"
              :error-message="errors.device_id"
              :loading="loading"
              hint="Unique device identifier"
            >
              <template v-slot:prepend>
                <q-icon name="devices" />
              </template>
            </q-input>
  
            <q-input
              v-model="form.device_name"
              label="Device Name (Optional)"
              outlined
              dense
              :loading="loading"
            >
              <template v-slot:prepend>
                <q-icon name="phone_android" />
              </template>
            </q-input>
  
            <q-select
              v-model="form.platform"
              :options="platformOptions"
              label="Platform"
              outlined
              dense
              :loading="loading"
            >
              <template v-slot:prepend>
                <q-icon name="computer" />
              </template>
            </q-select>
          </div>

          <div class="text-right">
            <q-btn
              flat
              dense
              no-caps
              label="Forgot Password?"
              color="primary"
              @click="$router.push('/forgot-password')"
              class="q-pa-none"
            />
          </div>

          <div>
            <q-btn
              type="submit"
              label="Login"
              color="primary"
              class="full-width"
              :loading="loading"
              unelevated
            />
          </div>

          <div class="text-center">
            <span class="text-grey-7">Don't have an account? </span>
            <q-btn
              flat
              dense
              no-caps
              label="Register"
              color="primary"
              @click="$router.push('/register')"
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
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { useAuthStore } from 'stores/auth'

export default defineComponent({
  name: 'LoginPage',
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
      login: '',
      password: '',
      device_id: '',
      device_name: '',
      platform: 'web',
    })

    const errors = ref({})
    const loading = ref(false)

    const platformOptions = ['web', 'android', 'ios']

    // Generate or get device ID
    const getDeviceId = () => {
      if (process.env.CLIENT) {
        let deviceId = localStorage.getItem('device_id')
        if (!deviceId) {
          deviceId = 'web_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
          localStorage.setItem('device_id', deviceId)
        }
        return deviceId
      }
      return 'web_device'
    }

    onMounted(() => {
      // Set device ID if not set
      if (!form.value.device_id) {
        form.value.device_id = getDeviceId()
      }

      // Set device name
      if (!form.value.device_name && process.env.CLIENT) {
        form.value.device_name = navigator.userAgent || 'Web Browser'
      }
    })

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const result = await authStore.login(form.value)

        if (result.success) {
          showNotify({
            type: 'positive',
            message: 'Login successful!',
            position: 'top',
          })
          router.push('/dashboard')
        } else {
          if (result.errors) {
            errors.value = result.errors
          }
          showNotify({
            type: 'negative',
            message: result.message || 'Login failed',
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
      platformOptions,
      onSubmit,
    }
  },
})
</script>

<style scoped>
.login-card {
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>

