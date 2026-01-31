<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="flex flex-center bg-grey-2">
        <q-card class="forgot-password-card" style="min-width: 330px; width: calc(100% - 32px); max-width: 450px;">
      <q-card-section class="text-center q-pb-none">
        <div class="text-h4 text-primary q-mb-md">CRM Homland</div>
        <div class="text-h6 text-grey-7">Reset your password</div>
      </q-card-section>

      <q-card-section v-if="!codeSent && !showResetForm">
        <q-form @submit="onSendCode" class="q-gutter-md">
          <q-input
            v-model="email"
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

          <div>
            <q-btn
              type="submit"
              label="Send Verification Code"
              color="primary"
              class="full-width"
              :loading="loading"
              unelevated
            />
          </div>

          <div class="text-center">
            <q-btn
              flat
              dense
              no-caps
              label="Back to Login"
              color="primary"
              @click="$router.push('/login')"
              class="q-pa-none"
            />
          </div>
        </q-form>
      </q-card-section>

      <q-card-section v-if="codeSent && !showResetForm">
        <div class="text-center q-mb-md">
          <q-icon name="check_circle" color="positive" size="64px" />
          <div class="text-h6 q-mt-md">Verification code sent!</div>
          <div class="text-body2 text-grey-7">
            Please check your email for the verification code.
          </div>
        </div>

        <div class="q-mt-lg">
          <q-btn
            label="Enter Verification Code"
            color="primary"
            class="full-width"
            @click="showResetForm = true"
            unelevated
          />
        </div>
      </q-card-section>

      <q-card-section v-if="showResetForm">
        <q-form @submit="onResetPassword" class="q-gutter-md">
          <q-input
            v-model="resetForm.email"
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
            v-model="resetForm.code"
            label="Verification Code"
            outlined
            dense
            :error="!!errors.code"
            :error-message="errors.code"
            :loading="loading"
            required
            mask="######"
          >
            <template v-slot:prepend>
              <q-icon name="vpn_key" />
            </template>
          </q-input>

          <q-input
            v-model="resetForm.password"
            label="New Password"
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
            v-model="resetForm.password_confirmation"
            label="Confirm New Password"
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

          <div>
            <q-btn
              type="submit"
              label="Reset Password"
              color="primary"
              class="full-width"
              :loading="loading"
              unelevated
            />
          </div>

          <div class="text-center">
            <q-btn
              flat
              dense
              no-caps
              label="Back to Login"
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
  name: 'ForgotPasswordPage',
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

    const email = ref('')
    const codeSent = ref(false)
    const showResetForm = ref(false)
    const loading = ref(false)
    const errors = ref({})

    const resetForm = ref({
      email: '',
      code: '',
      password: '',
      password_confirmation: '',
    })

    const onSendCode = async () => {
      errors.value = {}
      loading.value = true

      try {
        const result = await authStore.sendPasswordResetCode(email.value)

        if (result.success) {
          codeSent.value = true
          resetForm.value.email = email.value
          showNotify({
            type: 'positive',
            message: result.message || 'Verification code sent to your email',
            position: 'top',
          })
        } else {
          if (result.errors) {
            errors.value = result.errors
          }
          showNotify({
            type: 'negative',
            message: result.message || 'Failed to send verification code',
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

    const onResetPassword = async () => {
      errors.value = {}
      loading.value = true

      try {
        const result = await authStore.resetPassword(resetForm.value)

        if (result.success) {
          showNotify({
            type: 'positive',
            message: result.message || 'Password reset successful!',
            position: 'top',
          })
          router.push('/login')
        } else {
          if (result.errors) {
            errors.value = result.errors
          }
          showNotify({
            type: 'negative',
            message: result.message || 'Failed to reset password',
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
      email,
      codeSent,
      showResetForm,
      loading,
      errors,
      resetForm,
      onSendCode,
      onResetPassword,
    }
  },
})
</script>

<style scoped>
.forgot-password-card {
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>

