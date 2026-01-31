<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Create Group</div>
        <div class="text-body2 text-grey-7">Add a new customer group to the system</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/customer_groups')"
        />
      </div>
    </div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="">
          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.name"
                label="Name *"
                outlined
                :error="!!errors.name"
                :error-message="errors.name"
                required
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.color"
                label="Color"
                outlined
                :error="!!errors.color"
                :error-message="errors.color"
              >
                <template v-slot:append>
                  <q-icon name="colorize" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                      <q-color v-model="form.color" />
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
          </div>

          <div class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12">
              <q-input
                v-model="form.description"
                label="Description"
                type="textarea"
                rows="3"
                outlined
                :error="!!errors.description"
                :error-message="errors.description"
              />
            </div>
          </div>

          <div class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12 col-md-6">
              <q-toggle
                v-model="form.is_active"
                label="Active"
                :error="!!errors.is_active"
              />
            </div>
          </div>

          <div class="row q-mt-md">
            <div class="col">
              <q-btn
                type="submit"
                label="Create Customer Group"
                color="primary"
                :loading="loading"
                unelevated
              />
              <q-btn
                flat
                label="Cancel"
                color="grey"
                @click="$router.push('/crm/customer_groups')"
                class="q-ml-sm"
              />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'

export default defineComponent({
  name: 'CustomerGroupCreatePage',
  setup() {
    const router = useRouter()
    const $q = useQuasar()

    const form = ref({
      name: '',
      description: '',
      color: '#1976D2',
      is_active: true,
    })

    const errors = ref({})
    const loading = ref(false)

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const response = await api.post('/api/v1/customer-groups', form.value)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Customer group created successfully',
            position: 'top',
          })
          router.push('/crm/customer_groups')
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to create customer group',
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

