<template>
  <q-page padding>
    <div class="row q-mb-md items-center">
      <div class="col">
        <div class="text-h5">Create Lead</div>
        <div class="text-body2 text-grey-7">Add a new lead to the system</div>
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

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="">
          <!-- Customer Selection -->
          <div class="text-h6 q-mb-md">Customer Information</div>
          
          <!-- Mode Toggle Buttons -->
          <div class="row q-mb-md" style="gap: 10px;">
            <q-btn
              :color="customerMode === 'new' ? 'primary' : 'grey'"
              :outline="customerMode !== 'new'"
              label="New"
              icon="person_add"
              @click="customerMode = 'new'"
              unelevated
            />
            <q-btn
              :color="customerMode === 'search' ? 'primary' : 'grey'"
              :outline="customerMode !== 'search'"
              label="Search"
              icon="search"
              @click="customerMode = 'search'"
              unelevated
            />
          </div>

          <!-- New Customer Form -->
          <q-card v-if="customerMode === 'new'" class="q-mb-md">
            <q-card-section>
              <div class="text-subtitle1 q-mb-md">Create New Customer</div>
              <div class="row" style="gap: 10px;">
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="newCustomerForm.name"
                    label="Name *"
                    outlined
                    :error="!!newCustomerErrors.name"
                    :error-message="newCustomerErrors.name"
                    required
                  />
                </div>
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="newCustomerForm.mobile"
                    label="Mobile *"
                    outlined
                    :error="!!newCustomerErrors.mobile"
                    :error-message="newCustomerErrors.mobile"
                    @blur="onMobileBlur"
                    required
                  />
                </div>
                <div class="col-12">
                  <q-input
                    v-model="newCustomerForm.current_address_text"
                    label="Address"
                    type="textarea"
                    rows="2"
                    outlined
                    :error="!!newCustomerErrors.current_address_text"
                    :error-message="newCustomerErrors.current_address_text"
                  />
                </div>
                <div class="col-12">
                  <q-btn
                    color="primary"
                    label="Create Customer"
                    icon="add"
                    @click="createNewCustomer"
                    :loading="creatingCustomer"
                    unelevated
                  />
                </div>
              </div>
            </q-card-section>
          </q-card>

          <!-- Search Customer Form -->
          <q-card v-if="customerMode === 'search'" class="q-mb-md">
            <q-card-section>
              <div class="text-subtitle1 q-mb-md">Search Existing Customer</div>
              <div class="row" style="gap:10px;">
                <div class="col-12">
                  <q-select
                    v-model="form.customer_id"
                    :options="customerOptions"
                    option-label="displayLabel"
                    option-value="id"
                    label="Select Customer *"
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
              </div>
            </q-card-section>
          </q-card>

          <!-- Selected Customer Info -->
          <q-card v-if="selectedCustomerInfo" class="q-mb-md" style="background-color: #e3f2fd;">
            <q-card-section>
              <div class="row items-center">
                <div class="col">
                  <div class="text-subtitle1 text-weight-medium">{{ selectedCustomerInfo.name }}</div>
                  <div class="text-body2 text-grey-7">{{ selectedCustomerInfo.mobile }}</div>
                </div>
                <div class="col-auto">
                  <q-btn
                    flat
                    round
                    icon="close"
                    @click="clearSelectedCustomer"
                    size="sm"
                  />
                </div>
              </div>
            </q-card-section>
          </q-card>

          <!-- Lead Form Block -->
          <div v-if="form.customer_id" class="lead_info_block">
            <!-- Toggle Buttons - Show if there are previous records or notes -->
            <div v-if="customerLeads.length > 0 || customerNotes.length > 0" class="row q-mb-md" style="gap: 10px;">
              <q-btn
                v-if="customerLeads.length > 0"
                :color="viewMode === 'previous' ? 'primary' : 'grey'"
                :outline="viewMode !== 'previous'"
                label="Previous Records"
                icon="history"
                @click="viewMode = 'previous'"
                unelevated
              />
              <q-btn
                v-if="customerNotes.length > 0"
                :color="viewMode === 'notes' ? 'primary' : 'grey'"
                :outline="viewMode !== 'notes'"
                label="Previous Notes"
                icon="note"
                @click="viewMode = 'notes'"
                unelevated
              />
              <q-btn
                :color="viewMode === 'new' ? 'primary' : 'grey'"
                :outline="viewMode !== 'new'"
                label="New Record"
                icon="add"
                @click="viewMode = 'new'"
                unelevated
              />
            </div>

            <!-- Previous Notes List -->
            <div v-if="viewMode === 'notes' && customerNotes.length > 0" class="q-mb-md">
              <q-card>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Previous Notes ({{ customerNotes.length }})</div>
                  <q-timeline color="secondary">
                    <q-timeline-entry
                      v-for="note in customerNotes"
                      :key="note.id"
                      :title="formatDateForTable(note.created_at)"
                      :subtitle="note.creator?.name || 'Unknown'"
                      icon="note"
                    >
                      <div class="text-body1 q-mt-sm">{{ note.note }}</div>
                      <div class="text-caption text-grey-7 q-mt-xs">
                        <q-badge v-if="note.note_type" :color="getNoteTypeColor(note.note_type)" :label="note.note_type" class="q-mr-xs" />
                        <q-badge v-if="note.is_important" color="red" label="Important" />
                      </div>
                    </q-timeline-entry>
                  </q-timeline>
                </q-card-section>
              </q-card>
            </div>

            <!-- Previous Records List -->
            <div v-if="viewMode === 'previous' && customerLeads.length > 0" class="q-mb-md">
              <q-card>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Previous Records ({{ customerLeads.length }})</div>
                  <q-table
                    :rows="customerLeads"
                    :columns="leadColumns"
                    row-key="id"
                    :loading="loadingCustomerLeads"
                    flat
                    bordered
                  >
                    <template v-slot:body-cell-remarks="props">
                      <q-td :props="props">
                        <div class="text-body2" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" :title="props.value || '-'">
                          {{ props.value || '-' }}
                        </div>
                      </q-td>
                    </template>
                    <template v-slot:body-cell-next_contact_date="props">
                      <q-td :props="props">
                        {{ formatDateForTable(props.value) }}
                      </q-td>
                    </template>
                    <template v-slot:body-cell-actions="props">
                      <q-td :props="props">
                        <q-btn
                          flat
                          round
                          dense
                          icon="visibility"
                          @click="$router.push(`/crm/leads/${props.row.id}`)"
                          size="sm"
                        />
                        <q-btn
                          flat
                          round
                          dense
                          icon="edit"
                          @click="$router.push(`/crm/leads/${props.row.id}/edit`)"
                          size="sm"
                          class="q-ml-xs"
                        />
                      </q-td>
                    </template>
                  </q-table>
                </q-card-section>
              </q-card>
            </div>

            <!-- New Record Form -->
            <div v-if="viewMode === 'new' || (customerLeads.length === 0 && customerNotes.length === 0)">
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
                    label="Next Contact Date"
                    outlined
                    type="date"
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
                    label="Create Lead"
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
            </div>

          </div>

          <!-- Message when no customer selected -->
          <q-card v-else class="q-mt-md">
            <q-card-section class="text-center q-pa-xl">
              <q-icon name="info" size="48px" color="grey-6" class="q-mb-md" />
              <div class="text-h6 text-grey-7 q-mb-sm">Customer Required</div>
              <div class="text-body1 text-grey-6">
                Please create a new customer or select an existing customer before creating a lead.
              </div>
            </q-card-section>
          </q-card>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuasar, Notify } from 'quasar'
import { api } from 'boot/axios'
import debounce from 'debounce'
import { CallLog } from 'src/plugins/callLog'

export default defineComponent({
  name: 'LeadCreatePage',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const $q = useQuasar()

    const form = ref({
      customer_id: null,
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
    const customerMode = ref('new')
    const selectedCustomerInfo = ref(null)
    const creatingCustomer = ref(false)
    const newCustomerForm = ref({
      name: '',
      mobile: '',
      current_address_text: '',
    })
    const newCustomerErrors = ref({})

    // Previous records state
    const customerLeads = ref([])
    const loadingCustomerLeads = ref(false)
    const customerNotes = ref([])
    const loadingCustomerNotes = ref(false)
    const viewMode = ref('new') // 'new', 'previous', or 'notes'

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

    const onCustomerSelect = async (customer) => {
      if (customer) {
        selectedCustomerInfo.value = {
          name: customer.name || (typeof customer === 'object' ? customer.name : ''),
          mobile: customer.mobile || (typeof customer === 'object' ? customer.mobile : ''),
        }
        // Ensure customer_id is set correctly
        if (typeof customer === 'object' && customer.id) {
          form.value.customer_id = customer
        }
        // Fetch previous leads and notes for this customer
        const customerId = customer.id || customer
        await Promise.all([
          loadCustomerLeads(customerId),
          loadCustomerNotes(customerId)
        ])
      } else {
        clearSelectedCustomer()
      }
    }

    const clearSelectedCustomer = () => {
      form.value.customer_id = null
      selectedCustomerInfo.value = null
      customerLeads.value = []
      customerNotes.value = []
      viewMode.value = 'new'
    }

    const onMobileBlur = async () => {
      // Only search if mobile number is provided and customer is not already selected
      if (!newCustomerForm.value.mobile || !newCustomerForm.value.mobile.trim()) {
        return
      }

      // Don't search if customer is already selected
      if (form.value.customer_id) {
        return
      }

      const mobileNumber = newCustomerForm.value.mobile.trim()
      
      // Only search if mobile number looks valid (at least 10 digits)
      if (mobileNumber.length < 10) {
        return
      }

      try {
        await searchCustomerByMobile(mobileNumber)
      } catch (error) {
        console.error('Failed to search customer on mobile blur:', error)
      }
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
          // Set the created customer as selected
          form.value.customer_id = newCustomer
          selectedCustomerInfo.value = {
            name: newCustomer.name,
            mobile: newCustomer.mobile,
          }
          
          // Fetch previous leads and notes for this customer
          await Promise.all([
            loadCustomerLeads(newCustomer.id),
            loadCustomerNotes(newCustomer.id)
          ])
          
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

    const loadCustomerLeads = async (customerId) => {
      if (!customerId) {
        customerLeads.value = []
        return
      }

      loadingCustomerLeads.value = true
      try {
        const response = await api.get('/api/v1/leads', {
          params: {
            customer_id: customerId,
            per_page: 100, // Get all leads for this customer
            sort_by: 'created_at',
            descending: true,
          },
        })

        if (response.data.success) {
          customerLeads.value = response.data.data || []
          // If there are previous records, show them by default, otherwise show new record form
          if (customerLeads.value.length > 0) {
            viewMode.value = 'previous'
          } else {
            viewMode.value = 'new'
          }
        }
      } catch (error) {
        console.error('Failed to load customer leads:', error)
        customerLeads.value = []
        viewMode.value = 'new'
      } finally {
        loadingCustomerLeads.value = false
      }
    }

    // const formatStatus = (status) => {
    //   if (!status) return '-'
    //   return status.charAt(0).toUpperCase() + status.slice(1)
    // }

    // const getStatusColor = (status) => {
    //   const colors = {
    //     new: 'blue',
    //     contacted: 'orange',
    //     qualified: 'purple',
    //     converted: 'green',
    //     lost: 'red',
    //   }
    //   return colors[status] || 'grey'
    // }

    // const getPriorityColor = (priority) => {
    //   const colors = {
    //     low: 'grey',
    //     medium: 'orange',
    //     high: 'red',
    //   }
    //   return colors[priority] || 'grey'
    // }

    const formatDateForTable = (dateString) => {
      if (!dateString) return '-'
      try {
        const date = new Date(dateString)
        if (isNaN(date.getTime())) return '-'
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}`
      } catch (error) {
        console.log(error)
        return '-'
      }
    }

    const formatDateTimeForTable = (dateString) => {
      if (!dateString) return '-'
      try {
        const date = new Date(dateString)
        if (isNaN(date.getTime())) return '-'
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')
        const hours = String(date.getHours()).padStart(2, '0')
        const minutes = String(date.getMinutes()).padStart(2, '0')
        return `${year}-${month}-${day} ${hours}:${minutes}`
      } catch (error) {
        console.log(error)
        return '-'
      }
    }

    const getNoteTypeColor = (noteType) => {
      const colors = {
        general: 'grey',
        call: 'blue',
        meeting: 'purple',
        follow_up: 'orange',
      }
      return colors[noteType] || 'grey'
    }

    const loadCustomerNotes = async (customerId) => {
      if (!customerId) {
        customerNotes.value = []
        return
      }

      loadingCustomerNotes.value = true
      try {
        // Try to fetch customer with notes
        const response = await api.get(`/api/v1/customers/${customerId}`)
        
        if (response.data.success && response.data.data.customer_notes) {
          // Sort by created_at desc (newest first)
          customerNotes.value = (response.data.data.customer_notes || [])
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          
          // Update view mode if there are notes
          if (customerNotes.value.length > 0 && customerLeads.value.length === 0) {
            viewMode.value = 'notes'
          }
        } else {
          customerNotes.value = []
        }
      } catch (error) {
        console.error('Failed to load customer notes:', error)
        customerNotes.value = []
      } finally {
        loadingCustomerNotes.value = false
      }
    }

    const leadColumns = [
      {
        name: 'id',
        label: 'ID',
        field: 'id',
        align: 'left',
        sortable: true,
      },
      {
        name: 'remarks',
        label: 'Remarks',
        field: 'remarks',
        align: 'left',
        sortable: false,
      },
      {
        name: 'lead_source',
        label: 'Lead Source',
        field: (row) => row.customer_lead_source?.title || row.lead_source || '-',
        align: 'left',
        sortable: false,
      },
      {
        name: 'project',
        label: 'Project',
        field: (row) => row.project?.name || '-',
        align: 'left',
        sortable: false,
      },
      {
        name: 'next_contact_date',
        label: 'Contact Date',
        field: 'next_contact_date',
        align: 'left',
        sortable: true,
      },
      {
        name: 'actions',
        label: 'Actions',
        field: 'actions',
        align: 'center',
        sortable: false,
      },
    ]

    const onSubmit = async () => {
      errors.value = {}
      loading.value = true

      try {
        const submitData = {
          ...form.value,
          customer_id: form.value.customer_id && typeof form.value.customer_id === 'object'
            ? form.value.customer_id.id
            : (form.value.customer_id || null),
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

        const response = await api.post('/api/v1/leads', submitData)

        if (response.data.success) {
          showNotify({
            type: 'positive',
            message: 'Lead created successfully',
            position: 'top',
          })
          // Reload customer leads and notes to show the new record
          if (form.value.customer_id) {
            const customerId = typeof form.value.customer_id === 'object' 
              ? form.value.customer_id.id 
              : form.value.customer_id
            await Promise.all([
              loadCustomerLeads(customerId),
              loadCustomerNotes(customerId)
            ])
            viewMode.value = 'previous' // Switch to previous records view
          } else {
            router.push('/crm/leads')
          }
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

    const loadLastCallAndSearchCustomer = async () => {
      try {
        // Check for phone_number query param first
        const phoneNumberFromQuery = route.query.phone_number
        
        if (phoneNumberFromQuery) {
          // Use phone number from query param
          newCustomerForm.value.mobile = phoneNumberFromQuery.trim()
          await searchCustomerByMobile(phoneNumberFromQuery.trim())
          return
        }

        // Try to get last call from call log
        try {
          const res = await CallLog.get_call_log()
          if (res && res.calls && res.calls.length > 0) {
            // Get first call log (last incoming call)
            const firstCallLog = res.calls[0]
            
            if (firstCallLog && firstCallLog.number) {
              // Set name and mobile in form
              newCustomerForm.value.name = firstCallLog.name || ''
              newCustomerForm.value.mobile = firstCallLog.number.trim()
              
              // Search customer by mobile number
              await searchCustomerByMobile(firstCallLog.number.trim())
            }
          }
        } catch (callLogError) {
          // CallLog plugin might not be available (web platform)
          console.log('CallLog not available:', callLogError)
        }
      } catch (error) {
        console.error('Failed to load last call:', error)
      }
    }

    const searchCustomerByMobile = async (mobileNumber) => {
      if (!mobileNumber || mobileNumber.trim() === '') {
        return
      }

      try {
        // Search customer by mobile number
        const response = await api.get('/api/v1/customers/search', {
          params: {
            search: mobileNumber.trim(),
            is_active: true,
            per_page: 10,
          },
        })

        if (response.data.success && response.data.data && response.data.data.length > 0) {
          // Customer found - select the first one
          const customer = response.data.data[0]
          const customerObj = {
            ...customer,
            displayLabel: `${customer.name} - ${customer.mobile}`,
          }
          
          // Set customer in form
          form.value.customer_id = customerObj
          selectedCustomerInfo.value = {
            name: customer.name,
            mobile: customer.mobile,
            current_address_text: customer.current_address_text,
            current_address: customer.current_address,
          }
          
          // Add to options
          customerOptions.value.unshift(customerObj)
          
          // Switch to search mode
          customerMode.value = 'search'
          
          // Load customer leads and notes
          await Promise.all([
            loadCustomerLeads(customer.id),
            loadCustomerNotes(customer.id)
          ])
          
          // Set view mode based on what's available
          if (customerLeads.value.length > 0) {
            viewMode.value = 'previous'
          } else if (customerNotes.value.length > 0) {
            viewMode.value = 'notes'
          }
        } else {
          // Customer not found - form is already filled with call log data
          // Switch to new customer mode
          customerMode.value = 'new'
          console.log('No customer found for mobile:', mobileNumber)
        }
      } catch (error) {
        console.error('Failed to search customer by mobile:', error)
      }
    }

    onMounted(async () => {
      // Load initial data
      loadLeadSources('')
      loadProjects('')
      
      // Load last call and search customer
      await loadLastCallAndSearchCustomer()
    })

    return {
      form,
      errors,
      loading,
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
      customerMode,
      selectedCustomerInfo,
      creatingCustomer,
      newCustomerForm,
      newCustomerErrors,
      customerLeads,
      loadingCustomerLeads,
      customerNotes,
      loadingCustomerNotes,
      viewMode,
      leadColumns,
      statusOptions,
      priorityOptions,
      formatAddressLabel,
      formatDateForTable,
      formatDateTimeForTable,
      getNoteTypeColor,
      loadCustomers,
      loadCustomerNotes,
      onCustomerSearch,
      onCustomerFocus,
      onCustomerSelect,
      clearSelectedCustomer,
      onMobileBlur,
      createNewCustomer,
      loadCustomerLeads,
      loadProjects,
      onProjectSearch,
      onProjectFocus,
      loadLeadSources,
      onLeadSourceSearch,
      onLeadSourceFocus,
      loadInterestedTypes,
      onInterestedTypeSearch,
      onInterestedTypeFocus,
      onSubmit,
    }
  },
})
</script>

