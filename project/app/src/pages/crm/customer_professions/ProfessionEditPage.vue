<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Edit Profession</div>
        <div class="text-body2 text-grey-7">Update profession information</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/customer_professions')"
        />
      </div>
    </div>

    <q-card v-if="!loading && profession">
      <q-card-section>
        <q-form @submit="onSubmit" class="">
          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.type"
                :options="typeOptions"
                label="Type *"
                outlined
                :error="!!errors.type"
                :error-message="errors.type"
                required
                @update:model-value="onTypeChange"
              />
            </div>
          </div>

          <!-- Job Fields -->
          <div v-if="form.type === 'job'" class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.job_title"
                label="Job Title *"
                outlined
                :error="!!errors.job_title"
                :error-message="errors.job_title"
                required
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.company_name"
                label="Company Name"
                outlined
                :error="!!errors.company_name"
                :error-message="errors.company_name"
              />
            </div>
          </div>

          <!-- Business Fields -->
          <div v-if="form.type === 'business'" class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.business_type"
                label="Business Type *"
                outlined
                :error="!!errors.business_type"
                :error-message="errors.business_type"
                required
              />
            </div>
          </div>

          <!-- Description (for all types) -->
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

          <div class="row q-mt-md">
            <div class="col">
              <q-btn
                type="submit"
                label="Update Profession"
                color="primary"
                :loading="loading"
                unelevated
              />
              <q-btn
                flat
                label="Cancel"
                color="grey"
                @click="$router.push('/crm/customer_professions')"
                class="q-ml-sm"
              />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <q-inner-loading :showing="loading && !profession">
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
  name: 'ProfessionEditPage',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const $q = useQuasar()

    const profession = ref(null)
    const form = ref({
      type: null,
      business_type: '',
      job_title: '',
      company_name: '',
      description: '',
    })

    const errors = ref({})
    const loading = ref(false)

    const typeOptions = ['job', 'business', 'student', 'housewife']

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const onTypeChange = () => {
      // Clear conditional fields when type changes (only if changing to different type)
      // Don't clear if just loading existing data
      if (profession.value && profession.value.type !== form.value.type) {
        form.value.business_type = ''
        form.value.job_title = ''
        form.value.company_name = ''
      }
    }

    const loadProfession = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/professions/${route.params.id}`)
        if (response.data.success) {
          profession.value = response.data.data
          form.value = {
            type: profession.value.type,
            business_type: profession.value.business_type || '',
            job_title: profession.value.job_title || '',
            company_name: profession.value.company_name || '',
            description: profession.value.description || '',
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load profession',
          position: 'top',
        })
        router.push('/crm/customer_professions')
      } finally {
        loading.value = false
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const response = await api.put(`/api/v1/professions/${route.params.id}`, form.value)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Profession updated successfully',
            position: 'top',
          })
          router.push('/crm/customer_professions')
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to update profession',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadProfession()
    })

      return {
      profession,
      form,
      errors,
      loading,
      typeOptions,
      onTypeChange,
      onSubmit,
    }
  },
})
</script>

