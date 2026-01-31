<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Edit Customer</div>
        <div class="text-body2 text-grey-7">Update customer information</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/customer')"
        />
      </div>
    </div>

    <q-card v-if="!loading && customer">
      <q-card-section>
        <q-form @submit="onSubmit" class="">
          <!-- Basic Information -->
          <div class="text-h6 q-mb-md">Basic Information</div>
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
                v-model="form.mobile"
                label="Mobile *"
                outlined
                :error="!!errors.mobile"
                :error-message="errors.mobile"
                required
              />
            </div>
          </div>

          <div class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.email"
                label="Email"
                type="email"
                outlined
                :error="!!errors.email"
                :error-message="errors.email"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.alternative_mobile"
                label="Alternative Mobile"
                outlined
                :error="!!errors.alternative_mobile"
                :error-message="errors.alternative_mobile"
              />
            </div>
          </div>

          <!-- Group & Profession -->
          <div class="text-h6 q-mb-md q-mt-md">Group & Profession</div>
          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.customer_group_id"
                :options="customerGroupOptions"
                option-label="name"
                option-value="id"
                label="Customer Group"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onGroupSearch"
                @focus="onGroupFocus"
                :error="!!errors.customer_group_id"
                :error-message="errors.customer_group_id"
                :loading="loadingGroups"
              >
                <template v-slot:no-option>
                  <div class="text-center text-grey-7 q-pa-md">
                    {{ groupSearchQuery ? 'No groups found' : 'Start typing to search groups' }}
                  </div>
                </template>
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>{{ scope.opt.name }}</q-item-label>
                      <q-item-label v-if="scope.opt.description" caption>
                        {{ scope.opt.description }}
                      </q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-badge
                        :style="{ backgroundColor: scope.opt.color || '#9e9e9e', color: '#fff' }"
                        :label="scope.opt.color || 'N/A'"
                      />
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.profession_id"
                :options="professionOptions"
                option-label="displayName"
                option-value="id"
                label="Profession"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onProfessionSearch"
                @focus="onProfessionFocus"
                :error="!!errors.profession_id"
                :error-message="errors.profession_id"
                :loading="loadingProfessions"
              >
                <template v-slot:no-option>
                  <div class="text-center text-grey-7 q-pa-md">
                    {{ professionSearchQuery ? 'No professions found' : 'Start typing to search professions' }}
                  </div>
                </template>
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>{{ scope.opt.displayName }}</q-item-label>
                      <q-item-label caption>{{ scope.opt.type }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.project_id"
                :options="projectOptions"
                option-label="name"
                option-value="id"
                label="Project"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onProjectSearch"
                @focus="onProjectFocus"
                :error="!!errors.project_id"
                :error-message="errors.project_id"
                :loading="loadingProjects"
              >
                <template v-slot:no-option>
                  <div class="text-center text-grey-7 q-pa-md">
                    {{ projectSearchQuery ? 'No projects found' : 'Start typing to search projects' }}
                  </div>
                </template>
                <template v-slot:option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>{{ scope.opt.name }}</q-item-label>
                      <q-item-label v-if="scope.opt.project_type" caption>
                        {{ scope.opt.project_type }} - {{ formatStatus(scope.opt.status) }}
                      </q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-badge
                        :color="getStatusColor(scope.opt.status)"
                        :label="formatStatus(scope.opt.status)"
                      />
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
          </div>

          <!-- Address Information -->
          <div class="text-h6 q-mb-md q-mt-md">Address Information</div>
          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.current_address_id"
                :options="addressOptions"
                option-label="displayLabel"
                option-value="id"
                label="Current Address"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onAddressSearch"
                @focus="onAddressFocus"
                :error="!!errors.current_address_id"
                :error-message="errors.current_address_id"
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
                v-model="form.current_address_text"
                label="Address Details"
                outlined
                :error="!!errors.current_address_text"
                :error-message="errors.current_address_text"
              />
            </div>
          </div>

          <div class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.nearest_market"
                label="Nearest Market"
                outlined
                :error="!!errors.nearest_market"
                :error-message="errors.nearest_market"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.preferred_area"
                label="Preferred Area"
                outlined
                :error="!!errors.preferred_area"
                :error-message="errors.preferred_area"
              />
            </div>
          </div>

          <!-- Property Preferences -->
          <div class="text-h6 q-mb-md q-mt-md">Property Preferences</div>
          <div class="row" style="gap:10px;">
            <div class="col-12">
              <q-input
                v-model="form.target_real_estate"
                label="Target Real Estate"
                outlined
                :error="!!errors.target_real_estate"
                :error-message="errors.target_real_estate"
              />
            </div>
          </div>

          <!-- Additional Information -->
          <div class="text-h6 q-mb-md q-mt-md">Additional Information</div>
          <div class="row" style="gap:10px;">
            <div class="col-12">
              <q-input
                v-model="form.notes"
                label="New Note for Customer"
                type="textarea"
                rows="3"
                outlined
                :error="!!errors.notes"
                :error-message="errors.notes"
                hint="Add a new note. This will be saved to customer notes history."
              />
            </div>
          </div>

          <!-- Customer Notes History -->
          <div v-if="customerNotes.length > 0" class="q-mt-md">
            <div class="text-h6 q-mb-md">Notes History</div>
            <q-card>
              <q-card-section>
                <q-timeline color="primary">
                  <q-timeline-entry
                    v-for="note in customerNotes"
                    :key="note.id"
                    :title="formatDate(note.created_at)"
                    :subtitle="note.creator?.name || 'Unknown'"
                    :icon="getNoteTypeIcon(note.note_type)"
                    :color="note.is_important ? 'negative' : 'primary'"
                  >
                    <div class="text-body1 q-mt-sm">{{ note.note }}</div>
                    <div class="q-mt-xs">
                      <q-badge
                        :color="getNoteTypeColor(note.note_type)"
                        :label="note.note_type"
                        class="q-mr-sm"
                      />
                      <q-badge
                        v-if="note.is_important"
                        color="negative"
                        label="Important"
                      />
                    </div>
                  </q-timeline-entry>
                </q-timeline>
              </q-card-section>
            </q-card>
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
                label="Update Customer"
                color="primary"
                :loading="loading"
                unelevated
              />
              <q-btn
                flat
                label="Cancel"
                color="grey"
                @click="$router.push('/crm/customer')"
                class="q-ml-sm"
              />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <q-inner-loading :showing="loading && !customer">
      <q-spinner-gears size="50px" color="primary" />
    </q-inner-loading>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'
import debounce from 'debounce'

export default defineComponent({
  name: 'CustomerEditPage',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const $q = useQuasar()

    const customer = ref(null)
    const customerNotes = ref([])
    const form = ref({
      name: '',
      mobile: '',
      email: '',
      alternative_mobile: '',
      customer_group_id: null,
      project_id: null,
      profession_id: null,
      current_address_id: null,
      current_address_text: '',
      nearest_market: '',
      preferred_area: '',
      target_real_estate: '',
      notes: '',
      is_active: true,
    })

    const errors = ref({})
    const loading = ref(false)
    const loadingGroups = ref(false)
    const loadingProjects = ref(false)
    const loadingProfessions = ref(false)
    const loadingAddresses = ref(false)
    const customerGroupOptions = ref([])
    const projectOptions = ref([])
    const professionOptions = ref([])
    const addressOptions = ref([])
    const groupSearchQuery = ref('')
    const projectSearchQuery = ref('')
    const professionSearchQuery = ref('')
    const addressSearchQuery = ref('')

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const formatAddressLabel = (address) => {
      if (address.parent && address.parent.name) {
        return `${address.parent.name} > ${address.name}`
      }
      return address.name
    }

    const getProfessionDisplayName = (profession) => {
      if (profession.type === 'job' && profession.job_title) {
        return profession.job_title
      }
      if (profession.type === 'business' && profession.business_type) {
        return profession.business_type
      }
      return profession.type || '-'
    }

    const loadCustomerGroups = async (searchQuery = '') => {
      loadingGroups.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        const response = await api.get('/api/v1/customer-groups', { params })
        if (response.data.success) {
          customerGroupOptions.value = response.data.data || []
        }
      } catch (error) {
        console.error('Failed to load customer groups:', error)
        customerGroupOptions.value = []
      } finally {
        loadingGroups.value = false
      }
    }

    const loadProjects = async (searchQuery = '') => {
      loadingProjects.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        const response = await api.get('/api/v1/crm-projects', { params })
        if (response.data.success) {
          projectOptions.value = response.data.data || []
        }
      } catch (error) {
        console.error('Failed to load projects:', error)
        projectOptions.value = []
      } finally {
        loadingProjects.value = false
      }
    }

    const loadProfessions = async (searchQuery = '') => {
      loadingProfessions.value = true
      try {
        const params = { per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        const response = await api.get('/api/v1/professions', { params })
        if (response.data.success) {
          const professions = response.data.data || []
          professionOptions.value = professions.map((prof) => ({
            ...prof,
            displayName: getProfessionDisplayName(prof),
          }))
        }
      } catch (error) {
        console.error('Failed to load professions:', error)
        professionOptions.value = []
      } finally {
        loadingProfessions.value = false
      }
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

    const debouncedLoadGroups = debounce((searchQuery) => {
      loadCustomerGroups(searchQuery)
    }, 400)

    const debouncedLoadProjects = debounce((searchQuery) => {
      loadProjects(searchQuery)
    }, 400)

    const debouncedLoadProfessions = debounce((searchQuery) => {
      loadProfessions(searchQuery)
    }, 400)

    const debouncedLoadAddresses = debounce((searchQuery) => {
      loadAddresses(searchQuery)
    }, 400)

    const onGroupSearch = (val, update) => {
      groupSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadCustomerGroups('')
        } else {
          debouncedLoadGroups(val)
        }
      })
    }

    const onGroupFocus = () => {
      if (customerGroupOptions.value.length === 0 && !loadingGroups.value) {
        loadCustomerGroups('')
      }
    }

    const onProjectSearch = (val, update) => {
      projectSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadProjects('')
        } else {
          debouncedLoadProjects(val)
        }
      })
    }

    const onProjectFocus = () => {
      if (projectOptions.value.length === 0 && !loadingProjects.value) {
        loadProjects('')
      }
    }

    const onProfessionSearch = (val, update) => {
      professionSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadProfessions('')
        } else {
          debouncedLoadProfessions(val)
        }
      })
    }

    const onProfessionFocus = () => {
      if (professionOptions.value.length === 0 && !loadingProfessions.value) {
        loadProfessions('')
      }
    }

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

    const formatDate = (dateString) => {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    }

    const getNoteTypeIcon = (noteType) => {
      const iconMap = {
        general: 'note',
        call: 'phone',
        meeting: 'event',
        follow_up: 'schedule',
      }
      return iconMap[noteType] || 'note'
    }

    const getNoteTypeColor = (noteType) => {
      const colorMap = {
        general: 'blue',
        call: 'green',
        meeting: 'purple',
        follow_up: 'orange',
      }
      return colorMap[noteType] || 'grey'
    }

    const loadCustomer = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/customers/${route.params.id}`)
        if (response.data.success) {
          customer.value = response.data.data
          
          // Load customer notes (sorted by created_at desc)
          if (customer.value.customer_notes && Array.isArray(customer.value.customer_notes)) {
            customerNotes.value = customer.value.customer_notes
              .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          } else {
            customerNotes.value = []
          }
          
          form.value = {
            name: customer.value.name,
            mobile: customer.value.mobile,
            email: customer.value.email || '',
            alternative_mobile: customer.value.alternative_mobile || '',
            customer_group_id: customer.value.customer_group_id,
            project_id: customer.value.project_id,
            profession_id: customer.value.profession_id,
            current_address_id: customer.value.current_address_id,
            current_address_text: customer.value.current_address_text || '',
            nearest_market: customer.value.nearest_market || '',
            preferred_area: customer.value.preferred_area || '',
            target_real_estate: customer.value.target_real_estate || '',
            notes: customer.value.notes || '',
            is_active: customer.value.is_active !== undefined ? customer.value.is_active : true,
          }

          // Load options and set selected values
          await Promise.all([
            loadCustomerGroups(''),
            loadProjects(''),
            loadProfessions(''),
            loadAddresses(''),
          ])

          // Set selected options
          if (form.value.customer_group_id) {
            const groupOption = customerGroupOptions.value.find((opt) => opt.id === form.value.customer_group_id)
            if (groupOption) {
              form.value.customer_group_id = groupOption
            }
          }
          if (form.value.project_id) {
            const projectOption = projectOptions.value.find((opt) => opt.id === form.value.project_id)
            if (projectOption) {
              form.value.project_id = projectOption
            } else if (customer.value.project) {
              // If project not in options, add it manually
              projectOptions.value.push(customer.value.project)
              form.value.project_id = customer.value.project
            }
          }
          if (form.value.profession_id) {
            const professionOption = professionOptions.value.find((opt) => opt.id === form.value.profession_id)
            if (professionOption) {
              form.value.profession_id = professionOption
            }
          }
          if (form.value.current_address_id) {
            const addressOption = addressOptions.value.find((opt) => opt.id === form.value.current_address_id)
            if (addressOption) {
              form.value.current_address_id = addressOption
            } else if (customer.value.current_address) {
              // If address not in options, add it manually
              const addressObj = {
                ...customer.value.current_address,
                displayLabel: formatAddressLabel(customer.value.current_address),
              }
              addressOptions.value.push(addressObj)
              form.value.current_address_id = addressObj
            }
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load customer',
          position: 'top',
        })
        router.push('/crm/customer')
      } finally {
        loading.value = false
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const submitData = {
          ...form.value,
          customer_group_id: form.value.customer_group_id && typeof form.value.customer_group_id === 'object'
            ? form.value.customer_group_id.id
            : (form.value.customer_group_id || null),
          project_id: form.value.project_id && typeof form.value.project_id === 'object'
            ? form.value.project_id.id
            : (form.value.project_id || null),
          profession_id: form.value.profession_id && typeof form.value.profession_id === 'object'
            ? form.value.profession_id.id
            : (form.value.profession_id || null),
          current_address_id: form.value.current_address_id && typeof form.value.current_address_id === 'object'
            ? form.value.current_address_id.id
            : (form.value.current_address_id || null),
        }

        const response = await api.put(`/api/v1/customers/${route.params.id}`, submitData)

        if (response.data.success) {
          // Reload customer to get updated notes
          await loadCustomer()
          
          // Clear the notes field after successful save
          form.value.notes = ''
          
          showNotify({
            type: 'positive',
            message: 'Customer updated successfully',
            position: 'top',
          })
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to update customer',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadCustomer()
    })

    return {
      customer,
      customerNotes,
      form,
      errors,
      loading,
      loadingGroups,
      loadingProjects,
      loadingProfessions,
      loadingAddresses,
      customerGroupOptions,
      projectOptions,
      professionOptions,
      addressOptions,
      groupSearchQuery,
      projectSearchQuery,
      professionSearchQuery,
      addressSearchQuery,
      formatDate,
      getNoteTypeIcon,
      getNoteTypeColor,
      loadCustomerGroups,
      loadProjects,
      loadProfessions,
      loadAddresses,
      onGroupSearch,
      onGroupFocus,
      onProjectSearch,
      onProjectFocus,
      onProfessionSearch,
      onProfessionFocus,
      onAddressSearch,
      onAddressFocus,
      loadCustomer,
      onSubmit,
    }
  },
})
</script>

