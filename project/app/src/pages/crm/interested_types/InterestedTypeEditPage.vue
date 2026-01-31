<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Edit</div>
        <div class="text-body2 text-grey-7">Update interested type information</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/interested_types')"
        />
      </div>
    </div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md" v-if="form.name !== ''">
          <q-input
            v-model="form.name"
            label="Name *"
            outlined
            :error="!!errors.name"
            :error-message="errors.name"
          />

          <q-input
            v-model="form.description"
            label="Description"
            type="textarea"
            rows="3"
            outlined
            :error="!!errors.description"
            :error-message="errors.description"
          />

          <div class="row" style="gap: 10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.color"
                label="Color"
                outlined
                :error="!!errors.color"
                :error-message="errors.color"
                hint="Hex color code (e.g., #4CAF50)"
              >
                <template v-slot:prepend>
                  <q-icon name="palette" />
                </template>
                <template v-slot:append>
                  <div
                    v-if="form.color"
                    :style="{ width: '24px', height: '24px', backgroundColor: form.color, border: '1px solid #ccc', borderRadius: '4px' }"
                  />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model.number="form.sort_order"
                label="Sort Order"
                type="number"
                outlined
                :error="!!errors.sort_order"
                :error-message="errors.sort_order"
                hint="Lower numbers appear first"
              />
            </div>
          </div>

          <q-toggle
            v-model="form.is_active"
            label="Active"
            :error="!!errors.is_active"
          />

          <div>
            <q-btn
              label="Update Interested Type"
              type="submit"
              color="primary"
              :loading="loading"
              unelevated
            />
            <q-btn
              flat
              label="Cancel"
              color="grey"
              @click="$router.push('/crm/interested_types')"
              class="q-ml-sm"
            />
          </div>
        </q-form>
        <div v-else class="text-center q-pa-lg">
          <q-spinner color="primary" size="3em" />
          <div class="q-mt-md">Loading interested type...</div>
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'boot/axios'

export default defineComponent({
  name: 'InterestedTypeEditPage',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const $q = useQuasar()

    const form = ref({
      name: '',
      description: '',
      color: '',
      is_active: true,
      sort_order: 0,
    })
    const errors = ref({})
    const loading = ref(false)

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      }
    }

    const loadInterestedType = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/interested-types/${route.params.id}`)
        if (response.data.success) {
          const interestedType = response.data.data
          form.value = {
            name: interestedType.name,
            description: interestedType.description || '',
            color: interestedType.color || '',
            is_active: interestedType.is_active,
            sort_order: interestedType.sort_order || 0,
          }
        }
      } catch (error) {
        console.error('Failed to load interested type:', error)
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load interested type',
          position: 'top',
        })
        router.push('/crm/interested_types')
      } finally {
        loading.value = false
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const response = await api.put(`/api/v1/interested-types/${route.params.id}`, form.value)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Interested type updated successfully',
            position: 'top',
          })
          router.push('/crm/interested_types')
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to update interested type',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadInterestedType()
    })

    return {
      form,
      errors,
      loading,
      onSubmit,
    }
  },
})
</script>

