<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Edit Lead</div>
        <div class="text-body2 text-grey-7">Update lead information</div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          label=""
          icon="arrow_back"
          @click="$router.push('/crm/leads')"
        />
      </div>
    </div>

    <q-card v-if="!loading && lead">
      <q-card-section>
        <q-form @submit="onSubmit" class="">
          <!-- Customer Selection -->
          <div class="text-h6 q-mb-md">Customer Information</div>
          
          <!-- Mode Toggle Buttons -->
          <div class="row q-mb-md" style="gap: 10px;">
            <q-btn
              :color="customerMode === 'search' ? 'primary' : 'grey'"
              :outline="customerMode !== 'search'"
              label="Search Customer"
              icon="search"
              @click="customerMode = 'search'"
              unelevated
            />
            <q-btn
              :color="customerMode === 'new' ? 'primary' : 'grey'"
              :outline="customerMode !== 'new'"
              label="New Customer"
              icon="person_add"
              @click="customerMode = 'new'"
              unelevated
            />
          </div>

          <!-- Search Customer Form -->
          <div v-if="customerMode === 'search'" class="q-mb-md">
            <q-select
              v-model="customerId"
              :options="customerOptions"
              option-label="displayLabel"
              option-value="id"
              label="Customer *"
              outlined
              use-input
              input-debounce="400"
              @filter="onCustomerSearch"
              @focus="onCustomerFocus"
              @update:model-value="onCustomerSelect"
              :error="!!errors.customer_id"
              :error-message="errors.customer_id"
              :loading="loadingCustomers"
              clearable
            >
              <template v-slot:no-option>
                <div class="text-center text-grey-7 q-pa-md">
                  {{ customerSearchQuery ? 'No customers found' : 'Start typing to search customers' }}
                </div>
              </template>
              <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section>
                    <q-item-label>{{ scope.opt.name }}</q-item-label>
                    <q-item-label caption>{{ scope.opt.mobile }}</q-item-label>
                    <q-item-label v-if="scope.opt.current_address" caption>
                      {{ formatAddressLabel(scope.opt.current_address) }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>

          <!-- New Customer Form -->
          <div v-if="customerMode === 'new'" class="q-mb-md">
            <q-card flat bordered class="q-pa-md">
              <div class="text-subtitle1 q-mb-sm">Create New Customer</div>
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="newCustomerForm.name"
                    label="Name *"
                    outlined
                    dense
                    :error="!!newCustomerErrors.name"
                    :error-message="newCustomerErrors.name"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="newCustomerForm.mobile"
                    label="Mobile *"
                    outlined
                    dense
                    :error="!!newCustomerErrors.mobile"
                    :error-message="newCustomerErrors.mobile"
                  />
                </div>
                <div class="col-12">
                  <q-input
                    v-model="newCustomerForm.current_address_text"
                    label="Address Details"
                    type="textarea"
                    rows="2"
                    outlined
                    dense
                    :error="!!newCustomerErrors.current_address_text"
                    :error-message="newCustomerErrors.current_address_text"
                  />
                </div>
                <div class="col-12">
                  <q-btn
                    label="Create Customer"
                    color="primary"
                    @click="createNewCustomer"
                    :loading="creatingCustomer"
                    unelevated
                  />
                </div>
              </div>
            </q-card>
          </div>

          <!-- Selected Customer Info Card -->
          <q-card v-if="selectedCustomerInfo" flat bordered class="q-mb-md bg-blue-1">
            <q-card-section class="row items-center q-pb-none">
              <div class="text-subtitle1">Selected Customer:</div>
              <q-space />
              <q-btn icon="close" flat round dense @click="clearSelectedCustomer" />
            </q-card-section>
            <q-card-section class="q-pt-none">
              <div class="text-body1 text-weight-medium">{{ selectedCustomerInfo.name }}</div>
              <div class="text-body2">Mobile: {{ selectedCustomerInfo.mobile }}</div>
              <div v-if="selectedCustomerInfo.current_address_text" class="text-body2">
                Address: {{ selectedCustomerInfo.current_address_text }}
              </div>
              <div v-else-if="selectedCustomerInfo.current_address" class="text-body2">
                Address: {{ formatAddressLabel(selectedCustomerInfo.current_address) }}
              </div>
            </q-card-section>
          </q-card>

          <!-- Lead Source -->
          <div class="text-h6 q-mb-md q-mt-md">Lead Source</div>
          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.lead_source_id"
                :options="leadSourceOptions"
                option-label="title"
                option-value="id"
                label="Lead Source"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onLeadSourceSearch"
                @focus="onLeadSourceFocus"
                :error="!!errors.lead_source_id"
                :error-message="errors.lead_source_id"
                :loading="loadingLeadSources"
              >
                <template v-slot:no-option>
                  <div class="text-center text-grey-7 q-pa-md">
                    {{ leadSourceSearchQuery ? 'No lead sources found' : 'Start typing to search lead sources' }}
                  </div>
                </template>
              </q-select>
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.lead_source"
                label="Lead Source (Manual)"
                outlined
                :error="!!errors.lead_source"
                :error-message="errors.lead_source"
                hint="If lead source is not in the list above, enter manually"
              />
            </div>
          </div>

          <!-- Project & Interested Type -->
          <div class="text-h6 q-mb-md q-mt-md">Project & Interest</div>
          <div class="row" style="gap:10px;">
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
                        {{ scope.opt.project_type }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.interested_type_id"
                :options="interestedTypeOptions"
                option-label="name"
                option-value="id"
                label="Interested Type"
                outlined
                clearable
                use-input
                input-debounce="400"
                @filter="onInterestedTypeSearch"
                @focus="onInterestedTypeFocus"
                :error="!!errors.interested_type_id"
                :error-message="errors.interested_type_id"
                :loading="loadingInterestedTypes"
              >
                <template v-slot:no-option>
                  <div class="text-center text-grey-7 q-pa-md">
                    {{ interestedTypeSearchQuery ? 'No interested types found' : 'Start typing to search' }}
                  </div>
                </template>
              </q-select>
            </div>
          </div>

          <!-- Requirements -->
          <div class="text-h6 q-mb-md q-mt-md">Requirements</div>
          <div class="row" style="gap:10px;">
            <div class="col-12">
              <q-input
                v-model="form.customer_requirement"
                label="Customer Requirement"
                type="textarea"
                rows="3"
                outlined
                :error="!!errors.customer_requirement"
                :error-message="errors.customer_requirement"
              />
            </div>
          </div>
          <div class="row" style="gap:10px; margin-top: 10px;">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.preferred_area"
                label="Preferred Area"
                outlined
                :error="!!errors.preferred_area"
                :error-message="errors.preferred_area"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.next_contact_date"
                type="date"
                label="Next Contact Date"
                outlined
                :error="!!errors.next_contact_date"
                :error-message="errors.next_contact_date"
              />
            </div>
          </div>

          <!-- Status & Priority -->
          <div class="text-h6 q-mb-md q-mt-md">Status & Priority</div>
          <div class="row" style="gap:10px;">
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.status"
                :options="statusOptions"
                label="Status"
                outlined
                :error="!!errors.status"
                :error-message="errors.status"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.priority"
                :options="priorityOptions"
                label="Priority"
                outlined
                :error="!!errors.priority"
                :error-message="errors.priority"
              />
            </div>
          </div>

          <!-- Remarks -->
          <div class="text-h6 q-mb-md q-mt-md">Additional Information</div>
          <div class="row" style="gap:10px;">
            <div class="col-12">
              <q-input
                v-model="form.remarks"
                label="Remarks"
                type="textarea"
                rows="3"
                outlined
                :error="!!errors.remarks"
                :error-message="errors.remarks"
              />
            </div>
          </div>

          <div class="row q-mt-md">
            <div class="col">
              <q-btn
                type="submit"
                label="Update Lead"
                color="primary"
                :loading="loading"
                unelevated
              />
              <q-btn
                flat
                label="Cancel"
                color="grey"
                @click="$router.push('/crm/leads')"
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
import { defineComponent, ref, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'
import debounce from 'debounce'

export default defineComponent({
  name: 'LeadEditPage',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const $q = useQuasar()

    const lead = ref(null)

    // Separate state for customer_id
    const customerId = ref(null)

    const form = ref({
      lead_source: '',
      lead_source_id: null,
      project_id: null,
      customer_requirement: '',
      preferred_area: '',
      next_contact_date: '',
      remarks: '',
      interested_type_id: null,
      status: 'new',
      priority: 'medium',
    })

    const errors = ref({})
    const loading = ref(false)
    const loadingCustomers = ref(false)
    const loadingProjects = ref(false)
    const loadingLeadSources = ref(false)
    const loadingInterestedTypes = ref(false)
    const customerOptions = ref([])
    const projectOptions = ref([])
    const leadSourceOptions = ref([])
    const interestedTypeOptions = ref([])
    const customerSearchQuery = ref('')
    const projectSearchQuery = ref('')
    const leadSourceSearchQuery = ref('')
    const interestedTypeSearchQuery = ref('')

    // Customer selection mode: 'new' or 'search'
    const customerMode = ref('search') // Default to search mode for edit page
    const selectedCustomerInfo = ref(null)
    const creatingCustomer = ref(false)
    const newCustomerForm = ref({
      name: '',
      mobile: '',
      current_address_text: '',
    })
    const newCustomerErrors = ref({})

    const statusOptions = [
      { label: 'New', value: 'new' },
      { label: 'Contacted', value: 'contacted' },
      { label: 'Qualified', value: 'qualified' },
      { label: 'Converted', value: 'converted' },
      { label: 'Lost', value: 'lost' },
    ]

    const priorityOptions = [
      { label: 'Low', value: 'low' },
      { label: 'Medium', value: 'medium' },
      { label: 'High', value: 'high' },
    ]

    const showNotify = (options) => {
      if ($q && $q.notify) {
        $q.notify(options)
      } else {
        Notify.create(options)
      }
    }

    const formatAddressLabel = (address) => {
      if (!address) return '-'
      if (address.parent && address.parent.name) {
        return `${address.parent.name} > ${address.name}`
      }
      return address.name
    }

    const formatDateForInput = (dateString) => {
      if (!dateString) return ''
      // If it's already in YYYY-MM-DD format, return as is
      if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return dateString
      }
      // Try to parse and format the date
      try {
        const date = new Date(dateString)
        if (isNaN(date.getTime())) return ''
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}`
      } catch (error) {
        console.error('Error formatting date:', error)
        return ''
      }
    }

    const loadCustomers = async (searchQuery = '') => {
      loadingCustomers.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        const response = await api.get('/api/v1/customers/search', { params })
        if (response.data.success) {
          const customers = response.data.data || []
          customerOptions.value = customers.map((customer) => ({
            ...customer,
            displayLabel: `${customer.name} - ${customer.mobile}`,
          }))
        }
      } catch (error) {
        console.error('Failed to load customers:', error)
        customerOptions.value = []
      } finally {
        loadingCustomers.value = false
      }
    }

    const debouncedLoadCustomers = debounce((searchQuery) => {
      loadCustomers(searchQuery)
    }, 400)

    const onCustomerSearch = (val, update) => {
      customerSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadCustomers('')
        } else {
          debouncedLoadCustomers(val)
        }
      })
    }

    const onCustomerFocus = () => {
      if (customerOptions.value.length === 0 && !loadingCustomers.value) {
        loadCustomers('')
      }
    }

    const onCustomerSelect = (customer) => {
      if (customer) {
        // If customer is an object, extract ID
        if (typeof customer === 'object' && customer.id) {
          customerId.value = customer.id
          selectedCustomerInfo.value = {
            name: customer.name,
            mobile: customer.mobile,
            current_address_text: customer.current_address_text,
            current_address: customer.current_address,
          }
        } else {
          // If customer is just an ID, find it in options
          const customerObj = customerOptions.value.find(opt => opt.id === customer)
          if (customerObj) {
            customerId.value = customerObj.id
            selectedCustomerInfo.value = {
              name: customerObj.name,
              mobile: customerObj.mobile,
              current_address_text: customerObj.current_address_text,
              current_address: customerObj.current_address,
            }
          } else {
            customerId.value = customer
          }
        }
      } else {
        clearSelectedCustomer()
      }
    }

    const clearSelectedCustomer = () => {
      customerId.value = null
      selectedCustomerInfo.value = null
      customerMode.value = 'search' // Reset to search mode
    }

    const createNewCustomer = async () => {
      newCustomerErrors.value = {}
      creatingCustomer.value = true

      // Basic validation
      if (!newCustomerForm.value.name || !newCustomerForm.value.name.trim()) {
        newCustomerErrors.value.name = 'Name is required'
        creatingCustomer.value = false
        return
      }
      if (!newCustomerForm.value.mobile || !newCustomerForm.value.mobile.trim()) {
        newCustomerErrors.value.mobile = 'Mobile is required'
        creatingCustomer.value = false
        return
      }

      try {
        const customerData = {
          name: newCustomerForm.value.name.trim(),
          mobile: newCustomerForm.value.mobile.trim(),
          current_address_text: newCustomerForm.value.current_address_text?.trim() || '',
          is_active: true,
        }

        const response = await api.post('/api/v1/customers', customerData)

        if (response.data.success) {
          const newCustomer = response.data.data
          const customerObj = {
            ...newCustomer,
            displayLabel: `${newCustomer.name} - ${newCustomer.mobile}`,
          }
          
          // Add to options
          customerOptions.value.unshift(customerObj)
          
          // Set as selected
          customerId.value = newCustomer.id
          selectedCustomerInfo.value = {
            name: newCustomer.name,
            mobile: newCustomer.mobile,
            current_address_text: newCustomer.current_address_text,
            current_address: newCustomer.current_address,
          }
          
          // Reset new customer form
          newCustomerForm.value = {
            name: '',
            mobile: '',
            current_address_text: '',
          }
          
          // Switch to search mode to show the selected customer
          customerMode.value = 'search'
          
          showNotify({
            type: 'positive',
            message: 'Customer created successfully',
            position: 'top',
          })
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          newCustomerErrors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to create customer',
          position: 'top',
        })
      } finally {
        creatingCustomer.value = false
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

    const debouncedLoadProjects = debounce((searchQuery) => {
      loadProjects(searchQuery)
    }, 400)

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

    const loadLeadSources = async (searchQuery = '') => {
      loadingLeadSources.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        const response = await api.get('/api/v1/lead-sources', { params })
        if (response.data.success) {
          leadSourceOptions.value = response.data.data || []
        }
      } catch (error) {
        console.error('Failed to load lead sources:', error)
        leadSourceOptions.value = []
      } finally {
        loadingLeadSources.value = false
      }
    }

    const debouncedLoadLeadSources = debounce((searchQuery) => {
      loadLeadSources(searchQuery)
    }, 400)

    const onLeadSourceSearch = (val, update) => {
      leadSourceSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadLeadSources('')
        } else {
          debouncedLoadLeadSources(val)
        }
      })
    }

    const onLeadSourceFocus = () => {
      if (leadSourceOptions.value.length === 0 && !loadingLeadSources.value) {
        loadLeadSources('')
      }
    }

    const loadInterestedTypes = async (searchQuery = '') => {
      loadingInterestedTypes.value = true
      try {
        const params = { is_active: true, per_page: 50 }
        if (searchQuery && searchQuery.trim() !== '') {
          params.search = searchQuery.trim()
        }
        // Note: InterestedType API endpoint may not exist yet
        // This will gracefully handle the error if endpoint doesn't exist
        const response = await api.get('/api/v1/interested-types', { params })
        if (response.data.success) {
          interestedTypeOptions.value = response.data.data || []
        }
      } catch (error) {
        // Silently fail if API doesn't exist - InterestedType is optional
        console.warn('InterestedType API not available:', error.response?.status)
        interestedTypeOptions.value = []
      } finally {
        loadingInterestedTypes.value = false
      }
    }

    const debouncedLoadInterestedTypes = debounce((searchQuery) => {
      loadInterestedTypes(searchQuery)
    }, 400)

    const onInterestedTypeSearch = (val, update) => {
      interestedTypeSearchQuery.value = val
      update(() => {
        if (val === '') {
          loadInterestedTypes('')
        } else {
          debouncedLoadInterestedTypes(val)
        }
      })
    }

    const onInterestedTypeFocus = () => {
      if (interestedTypeOptions.value.length === 0 && !loadingInterestedTypes.value) {
        loadInterestedTypes('')
      }
    }

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      // Validate customer_id
      if (!customerId.value) {
        errors.value.customer_id = 'Customer is required'
        showNotify({
          type: 'negative',
          message: 'Please select or create a customer',
          position: 'top',
        })
        loading.value = false
        return
      }

      try {
        const submitData = {
          ...form.value,
          customer_id: customerId.value, // Append customer_id from separate state
          project_id: form.value.project_id && typeof form.value.project_id === 'object'
            ? form.value.project_id.id
            : (form.value.project_id || null),
          lead_source_id: form.value.lead_source_id && typeof form.value.lead_source_id === 'object'
            ? form.value.lead_source_id.id
            : (form.value.lead_source_id || null),
          interested_type_id: form.value.interested_type_id && typeof form.value.interested_type_id === 'object'
            ? form.value.interested_type_id.id
            : (form.value.interested_type_id || null),
        }

        const response = await api.put(`/api/v1/leads/${route.params.id}`, submitData)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Lead updated successfully',
            position: 'top',
          })
          router.push('/crm/leads')
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to create lead',
          position: 'top',
        })
      } finally {
        loading.value = false
      }
    }

    const loadLead = async () => {
      loading.value = true
      try {
        const response = await api.get(`/api/v1/leads/${route.params.id}`)
        if (response.data.success) {
          lead.value = response.data.data
          
          // Set customer_id in separate state
          customerId.value = lead.value.customer_id
          
          form.value = {
            lead_source: lead.value.lead_source || '',
            lead_source_id: lead.value.lead_source_id,
            project_id: lead.value.project_id,
            customer_requirement: lead.value.customer_requirement || '',
            preferred_area: lead.value.preferred_area || '',
            next_contact_date: formatDateForInput(lead.value.next_contact_date),
            remarks: lead.value.remarks || '',
            interested_type_id: lead.value.interested_type_id,
            status: lead.value.status || 'new',
            priority: lead.value.priority || 'medium',
          }

          // Load options and set selected values
          await Promise.all([
            loadLeadSources(''),
            loadProjects(''),
            loadInterestedTypes(''),
            loadCustomers(''),
          ])

          // Set selected options - Customer must be set first
          if (customerId.value && lead.value.customer) {
            const customerObj = {
              ...lead.value.customer,
              displayLabel: `${lead.value.customer.name} - ${lead.value.customer.mobile}`,
            }
            // Check if customer already exists in options
            const existingCustomer = customerOptions.value.find(opt => opt.id === customerObj.id)
            if (!existingCustomer) {
              customerOptions.value.unshift(customerObj) // Add at beginning
            }
            // Set selected customer info for display
            selectedCustomerInfo.value = {
              name: lead.value.customer.name,
              mobile: lead.value.customer.mobile,
              current_address_text: lead.value.customer.current_address_text,
              current_address: lead.value.customer.current_address,
            }
          }
          if (form.value.project_id && lead.value.project) {
            projectOptions.value.push(lead.value.project)
            form.value.project_id = lead.value.project
          }
          if (form.value.lead_source_id && lead.value.customer_lead_source) {
            leadSourceOptions.value.push(lead.value.customer_lead_source)
            form.value.lead_source_id = lead.value.customer_lead_source
          }
          if (form.value.interested_type_id && lead.value.interestedType) {
            interestedTypeOptions.value.push(lead.value.interestedType)
            form.value.interested_type_id = lead.value.interestedType
          }
        }
      } catch (error) {
        showNotify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to load lead',
          position: 'top',
        })
        router.push('/crm/leads')
      } finally {
        loading.value = false
      }
    }

    // Watch for customerId changes to update selectedCustomerInfo
    watch(() => customerId.value, async (newCustomerId) => {
      if (!newCustomerId) {
        selectedCustomerInfo.value = null
        return
      }

      // If it's an ID, try to find it in options first
      const customerObj = customerOptions.value.find(opt => opt.id === newCustomerId)
      if (customerObj) {
        selectedCustomerInfo.value = {
          name: customerObj.name,
          mobile: customerObj.mobile,
          current_address_text: customerObj.current_address_text,
          current_address: customerObj.current_address,
        }
      } else {
        // If not in options, fetch from API
        try {
          const response = await api.get(`/api/v1/customers/${newCustomerId}`)
          if (response.data.success) {
            const customer = response.data.data
            selectedCustomerInfo.value = {
              name: customer.name,
              mobile: customer.mobile,
              current_address_text: customer.current_address_text,
              current_address: customer.current_address,
            }
            // Add to options for future reference
            const customerOption = {
              ...customer,
              displayLabel: `${customer.name} - ${customer.mobile}`,
            }
            customerOptions.value.unshift(customerOption)
          }
        } catch (error) {
          console.error('Failed to fetch customer info:', error)
          selectedCustomerInfo.value = null
        }
      }
    }, { immediate: false })

    // Watch for customerMode changes to update customerId
    watch(() => customerMode.value, (newMode) => {
      if (newMode === 'new') {
        // When switching to new customer mode, clear customerId
        customerId.value = null
        selectedCustomerInfo.value = null
      }
      // When switching back to search, customerId will be set from selectedCustomerInfo if available
    })

    onMounted(() => {
      loadLead()
    })

    return {
      lead,
      form,
      errors,
      loading,
      customerId,
      customerOptions,
      projectOptions,
      leadSourceOptions,
      interestedTypeOptions,
      loadingCustomers,
      loadingProjects,
      loadingLeadSources,
      loadingInterestedTypes,
      customerSearchQuery,
      projectSearchQuery,
      leadSourceSearchQuery,
      interestedTypeSearchQuery,
      statusOptions,
      priorityOptions,
      formatAddressLabel,
      formatDateForInput,
      loadCustomers,
      onCustomerSearch,
      onCustomerFocus,
      onCustomerSelect,
      clearSelectedCustomer,
      createNewCustomer,
      customerMode,
      selectedCustomerInfo,
      creatingCustomer,
      newCustomerForm,
      newCustomerErrors,
      loadProjects,
      onProjectSearch,
      onProjectFocus,
      loadLeadSources,
      onLeadSourceSearch,
      onLeadSourceFocus,
      loadInterestedTypes,
      onInterestedTypeSearch,
      onInterestedTypeFocus,
      loadLead,
      onSubmit,
    }
  },
})
</script>

