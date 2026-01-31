<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Create Project</div>
        <div class="text-body2 text-grey-7">Add a new project to the system</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/projects')"
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
                @update:model-value="generateSlug"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.slug"
                label="Slug *"
                outlined
                :error="!!errors.slug"
                :error-message="errors.slug"
                required
                hint="Auto-generated from name, can be edited"
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
              <q-select
                v-model="form.address_id"
                :options="addressOptions"
                option-label="displayLabel"
                option-value="id"
                label="Address"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onAddressSearch"
                @focus="onAddressFocus"
                :error="!!errors.address_id"
                :error-message="errors.address_id"
                :loading="loadingAddresses"
              >
                <template v-slot:no-option>
                  <div class="text-center text-grey-7 q-pa-md">
                    {{ addressSearchQuery ? 'No addresses found' : 'Start typing to search addresses' }}
                  </div>
                </template>
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>{{ scope.opt.displayLabel }}</q-item-label>
                      <q-item-label caption>{{ scope.opt.type }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.address_text"
                label="Address Details"
                outlined
                :error="!!errors.address_text"
                :error-message="errors.address_text"
                hint="Additional address information"
              />
            </div>
          </div>

          <div class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.project_type"
                :options="projectTypeOptions"
                label="Project Type"
                outlined
                :error="!!errors.project_type"
                :error-message="errors.project_type"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.status"
                :options="statusOptions"
                label="Status *"
                outlined
                :error="!!errors.status"
                :error-message="errors.status"
                required
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
                label="Create Project"
                color="primary"
                :loading="loading"
                unelevated
              />
              <q-btn
                flat
                label="Cancel"
                color="grey"
                @click="$router.push('/crm/projects')"
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
import debounce from 'debounce'

export default defineComponent({
  name: 'ProjectCreatePage',
  setup() {
    const router = useRouter()
    const $q = useQuasar()

    const form = ref({
      name: '',
      slug: '',
      description: '',
      address_id: null,
      address_text: '',
      project_type: null,
      status: 'ongoing',
      is_active: true,
    })

    const errors = ref({})
    const loading = ref(false)
    const addressOptions = ref([])
    const loadingAddresses = ref(false)
    const addressSearchQuery = ref('')

    const projectTypeOptions = [
      { label: 'Apartment', value: 'apartment' },
      { label: 'Land', value: 'land' },
      { label: 'Commercial', value: 'commercial' },
      { label: 'Other', value: 'other' },
    ]

    const statusOptions = [
      { label: 'Planning', value: 'planning' },
      { label: 'Ongoing', value: 'ongoing' },
      { label: 'Completed', value: 'completed' },
      { label: 'On Hold', value: 'on_hold' },
    ]

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const generateSlug = () => {
      if (form.value.name && !form.value.slug) {
        form.value.slug = form.value.name
          .toLowerCase()
          .trim()
          .replace(/[^\w\s-]/g, '')
          .replace(/[\s_-]+/g, '-')
          .replace(/^-+|-+$/g, '')
      }
    }

    const formatAddressLabel = (address) => {
      if (!address) return ''
      const parts = []
      if (address.name) parts.push(address.name)
      if (address.parent && address.parent.name) parts.push(address.parent.name)
      return parts.join(', ') || address.name || ''
    }

    const loadAddresses = async (searchQuery = '') => {
      loadingAddresses.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        const response = await api.get('/api/v1/addresses', { params })
        if (response.data.success) {
          const addresses = response.data.data || []
          addressOptions.value = addresses.map((address) => ({
            ...address,
            displayLabel: formatAddressLabel(address),
          }))
        }
      } catch (error) {
        console.error('Failed to load addresses:', error)
        addressOptions.value = []
      } finally {
        loadingAddresses.value = false
      }
    }

    const debouncedLoadAddresses = debounce((searchQuery) => {
      loadAddresses(searchQuery)
    }, 400)

    const onAddressSearch = (val, update) => {
      addressSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadAddresses('')
        } else {
          debouncedLoadAddresses(val)
        }
      })
    }

    const onAddressFocus = () => {
      if (addressOptions.value.length === 0 && !loadingAddresses.value) {
        loadAddresses('')
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const response = await api.post('/api/v1/crm-projects', form.value)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Project created successfully',
            position: 'top',
          })
          router.push('/crm/projects')
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to create project',
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
      addressOptions,
      loadingAddresses,
      addressSearchQuery,
      projectTypeOptions,
      statusOptions,
      generateSlug,
      onAddressSearch,
      onAddressFocus,
      onSubmit,
    }
  },
})
</script>
