<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Edit Lead Source</div>
        <div class="text-body2 text-grey-7">Update lead source information</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/lead_sources')"
        />
      </div>
    </div>

    <q-card v-if="!loading && leadSource">
      <q-card-section>
        <q-form @submit="onSubmit" class="">
          <div class="row" style="gap:10px;">
            <div class="col-12">
              <q-input
                v-model="form.title"
                label="Title *"
                outlined
                :error="!!errors.title"
                :error-message="errors.title"
                required
              />
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
                label="Update Lead Source"
                color="primary"
                :loading="loading"
                unelevated
              />
              <q-btn
                flat
                label="Cancel"
                color="grey"
                @click="$router.push('/crm/lead_sources')"
                class="q-ml-sm"
              />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <q-inner-loading :showing="loading && !leadSource">
      <q-spinner-gears size="50px" color="primary" />
    </q-inner-loading>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'

export default defineComponent({
  name: 'LeadSourceEditPage',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const $q = useQuasar()

    const leadSource = ref(null)
    const form = ref({
      title: '',
      description: '',
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

    const loadLeadSource = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/lead-sources/${route.params.id}`)
        if (response.data.success) {
          leadSource.value = response.data.data
          form.value = {
            title: leadSource.value.title || '',
            description: leadSource.value.description || '',
            is_active: leadSource.value.is_active !== undefined ? leadSource.value.is_active : true,
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load lead source',
          position: 'top',
        })
        router.push('/crm/lead_sources')
      } finally {
        loading.value = false
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const response = await api.put(`/api/v1/lead-sources/${route.params.id}`, form.value)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Lead source updated successfully',
            position: 'top',
          })
          router.push('/crm/lead_sources')
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to update lead source',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadLeadSource()
    })

    return {
      leadSource,
      form,
      errors,
      loading,
      onSubmit,
    }
  },
})
</script>

